<?php namespace Aiws\Lexicon\Node;

use Aiws\Lexicon\Contract\EnvironmentInterface;
use Aiws\Lexicon\Contract\NodeInterface;
use Aiws\Lexicon\Data\Context;
use Aiws\Lexicon\Data\Traversal;

abstract class Node implements NodeInterface
{
    public $callback;

    public $callbackParameters = array();

    public $callbackData;

    public $callbackEnabled = true;

    public $callbackHandlerPhp;

    public $children = array();

    public $content = '';

    public $count = 0;

    public $data;

    public $depth = 0;

    public $extractionContent;

    public $footer = array();

    public $hash;

    public $incrementDepth = true;

    public $name = 'root';

    public $parameters;

    public $parent;

    public $parsedContent;

    public $trash = false;

    public $traversal;

    /**
     * @var EnvironmentInterface
     */
    public $lexicon;

    public function make(array $match, $parent = null, $depth = 0, $count = 0)
    {
        /** @var $node Node */
        $node = new static;
        $node->setEnvironment($this->lexicon);
        $node->getSetup($match);
        $node->callback      = $this->callback;
        $node->count         = $count;
        $node->depth         =
            ($this->incrementDepth and $depth <= $this->lexicon->getMaxDepth())
                ? $depth + 1
                : $depth;
        $node->hash          = md5($node->content . $node->name . $depth . $count);
        $node->parsedContent = $node->content;
        $node->parent        = $parent;
        $node->traversal     = new Traversal($this->lexicon->getScopeGlue());
        $node->parseParameters();

        return $node;
    }

    protected function getVariableRegex()
    {
        $glue = preg_quote($this->lexicon->getScopeGlue(), '/');

        return $glue === '\\.' ? '[a-zA-Z0-9_' . $glue . ']+' : '[a-zA-Z0-9_\.' . $glue . ']+';
    }

    public function getClosingTagRegex($name)
    {
        return '/\{\{\s*(\/' . $name . ')\s*\}\}/m';
    }

    public function getOpenTagMatches($text, $regex = null)
    {
        $matches = array();

        if (!$regex) {
            $regex = $this->getRegex();
        }

        preg_match_all($regex, $text, $matches, PREG_SET_ORDER);

        return $matches;
    }

    public function getSingleTagMatches($text, $name)
    {
        $matches = array();

        /**
         * $data_matches[0] is the raw data tag
         * $data_matches[1] is the data variable (dot notated)
         */
        foreach ($this->getOpenTagMatches($text) as $match) {
            if (!preg_match($this->getClosingTagRegex($name), $text, $closingTagMatch)) {
                $matches[] = $match;
            }
        }

        return $matches;
    }

    /**
     * Parses a parameter string into an array
     *
     * @param   string  The string of parameters
     * @return array
     */
    protected function parseParameters()
    {
        $this->parameters = $this->compress(trim($this->parameters));

        // Extract all literal string in the conditional to make it easier
        if (strpos($this->parameters, '"') !== false) {

            if (preg_match_all(
                '/(([a-zA-Z0-9_]*)\s*=\s*[\"|\']\s*([a-zA-Z0-9_]*)\s*[\"|\'])+/ms',
                $this->parameters,
                $matches,
                PREG_SET_ORDER
            )
            ) {
                foreach ($matches as $match) {
                    $this->callbackParameters[$match[2]] = $match[3];
                }
            }
        } elseif (!empty($this->parameters)) {

            $this->callbackParameters = explode(' ', $this->parameters);

        }

        return $this->callbackParameters;
    }

    public function createChildNodes()
    {
        // @todo - find IF
        // @todo - find ELSEIF
        // @todo - find ELSE
        // @todo - find UNLESS

        foreach ($this->lexicon->getNodeTypes() as $nodeType) {
            if ($nodeType instanceof Node) {
                $nodeType->setEnvironment($this->lexicon);
                foreach ($nodeType->getMatches($this->parsedContent) as $count => $match) {
                    $this->createChildNode($nodeType, $match, $count);
                }
            }
        }

        return $this;
    }

    protected function createChildNode(Node $nodeType, $match, $count = 0)
    {
        $node = $nodeType->make(
            $match,
            $parent = $this,
            $this->depth,
            $count
        );

        $this->context = new Context($this->data, $node->name);

        $node->data = $this->traversal->getNodeData($this, $this->data, $count);

        /*        $handler = $this->lexicon->getPluginHandler();

                if ($handler instanceof PluginHandlerInterface and $node->callbackEnabled
                ) {

                    $node->callbackData = $handler->call($node->name, $node->callbackParameters, $node->content);

                    $node->callbackHandlerPhp = $handler->compile(
                        $node->name,
                        $node->callbackParameters,
                        $node->content
                    );
                }*/

        $node->createChildNodes();

        $this->extract($node);

        return $node;
    }

    protected function extract(Node $node)
    {
        if (method_exists($node, 'compileParentNode')) {
            $this->parsedContent = $node->compileParentNode($this->parsedContent);
        }

        if (!$node->trash) {

            $this->parsedContent = str_replace(
                $node->extractionContent,
                $node->getExtractionHash(),
                $this->parsedContent
            );

            $this->children[] = $node;
        }

        return $this;
    }

    protected function inject(Node $node)
    {
        $this->parsedContent = str_replace(
            $node->getExtractionHash(),
            $node->compile(),
            $this->parsedContent
        );

        return $this;
    }

    public function getRootNode()
    {
        $node = $this;

        while (!$node->isRoot()) {
            $node = $this->parent;
        }

        return $node;
    }

    public function isRoot()
    {
        return !$this->parent;
    }

    public function getExtractionHash($suffix = null)
    {
        return '__' . get_called_class() . '__' . $this->name . '__' . $this->hash . '__' . $suffix;
    }

    public function getItem()
    {
        $name = str_replace($this->lexicon->getScopeGlue(), ' ', $this->name);

        return str_replace(' ', '', $name) . 'Item';
    }

    public function getAttribute($name, $default = 0)
    {
        if (isset($this->callbackParameters[$name])) {
            return $this->callbackParameters[$name];
        } elseif (isset($this->callbackParameters[$default])) {
            return $this->callbackParameters[$default];
        }

        return null;
    }

    public function setEnvironment(EnvironmentInterface $lexicon)
    {
        $this->lexicon = $lexicon;
        return $this;
    }

    public function compress($text)
    {
        return preg_replace('/\s\s+/', ' ', $text);
    }

    protected function php($php = '')
    {
        return '<?php ' . $php . ' ?>';
    }
}