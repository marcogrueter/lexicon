<?php namespace Aiws\Lexicon\Context;

use Aiws\Lexicon\Lexicon;

abstract class ContextType extends Lexicon
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

    public $footer  = array();

    public $hash;

    public $incrementDepth = true;

    public $name = '__LEXICON_ROOT__';

    public $parameters;

    public $parent;

    public $parsedContent;

    public $trash = false;


    abstract public function getSetup(array $match);

    abstract public function getRegex();

    abstract public function getMatches($text);

    abstract public function compileContext();

    public function make($match, $parent = null, $depth = 0, $count = 0)
    {
        /** @var $context ContextType */
        $context = new static;

        $context->getSetup($match);
        $context->callback      = $this->callback;
        $context->count         = $count;
        $context->depth         = ($this->incrementDepth and $depth <= $this->maxDepth) ? $depth + 1 : $depth;
        $context->hash          = md5($context->content . $context->name . $depth . $count);
        $context->parsedContent = $context->content;
        $context->parent        = $parent;

        $context->parseParameters();

        return $context;
    }

    protected function getVariableRegex()
    {
        $glue = preg_quote($this->scopeGlue, '/');

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
        foreach($this->getOpenTagMatches($text) as $match) {
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
        // Extract all literal string in the conditional to make it easier
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

        return $this->callbackParameters;
    }

    public function createChildContexts()
    {
        // @todo - find IF
        // @todo - find ELSEIF
        // @todo - find ELSE
        // @todo - find UNLESS

        foreach ($this->contexts as $contextTypeClass) {
            $contextType = new $contextTypeClass;

            if ($contextType instanceof ContextType) {
                foreach ($contextType->getMatches($this->parsedContent) as $count => $match) {
                    $this->createChildContext($contextType, $match, $count);
                }
            } else {
                // @todo - throw exception
            }
        }

        return $this;
    }

    protected function createChildContext(ContextType $contextType, $match, $count = 0)
    {
        $context = $contextType->make(
            $match,
            $parent = $this,
            $this->depth,
            $count
        );

        $context->data = $this->data()->getContextData($this, $this->data, $count);

        if ($this->callbackHandlerClass and $context->callbackEnabled and $this->callback) {
            // @todo - react to the returned data type within the compile() method
            $context->callbackData = call_user_func_array(
                $this->callback,
                array($context->name, $context->callbackParameters, $context->content, $context)
            );

            $callbackHandler = new $context->callbackHandlerClass;

            $context->callbackHandlerPhp = $callbackHandler->compile(
                $context->name,
                $context->callbackParameters,
                $context->content
            );
        }

        $context->createChildContexts();

        $this->extract($context);

        return $context;
    }

    protected function extract(ContextType $context)
    {
        if (method_exists($context, 'compileParentContext')) {
            $this->parsedContent = $context->compileParentContext($this->parsedContent);
        }

        if (!$context->trash) {

            $this->parsedContent = str_replace(
                $context->extractionContent,
                $context->getExtractionHash(),
                $this->parsedContent
            );

            $this->children[] = $context;
        }

        return $this;
    }

    protected function inject(ContextType $context)
    {
        $this->parsedContent = str_replace(
            $context->getExtractionHash(),
            $context->compileContext(),
            $this->parsedContent
        );

        return $this;
    }

    public function getRootNode()
    {
        $node = $this;

        while(!$node->isRoot()) {
            $node = $this->parent;
        }

        return $node;
    }

    public function isRoot()
    {
        return !$this->parent;
    }

    public function getExtractionHash()
    {
        return '__' . get_called_class() . '__' . $this->name . '__' . $this->hash . '__';
    }

    public function getItem()
    {
        return $this->name . 'Item';
    }

    public function getAttribute($name)
    {
        return isset($this->callbackParameters[$name]) ? $this->callbackParameters[$name] : null;
    }

    public function compress($text)
    {
        return preg_replace('/\s\s+/', ' ', $text);
    }

    protected function php($php = '', $short = false)
    {
        $open = $short ? '<?= ' : '<?php ';

        return $open . $php . ' ?>';
    }
}