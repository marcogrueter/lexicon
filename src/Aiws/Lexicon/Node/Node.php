<?php namespace Aiws\Lexicon\Node;

use Aiws\Lexicon\Contract\EnvironmentInterface;
use Aiws\Lexicon\Contract\NodeInterface;
use Aiws\Lexicon\Util\Regex;

abstract class Node implements NodeInterface
{
    public $attributes = array();

    public $children = array();

    public $content = '';

    public $count = 0;

    public $data;

    public $depth = 0;

    public $extractionContent;

    public $footer = array();

    public $id;

    public $incrementDepth = true;

    public $name = 'root';

    public $parameters;

    public $parent;

    public $parsedContent;

    public $trash = false;

    /**
     * @var EnvironmentInterface
     */
    public $lexicon;

    public function make(array $match, $parent = null, $depth = 0, $count = 0)
    {
        $depth = ($this->incrementDepth and $depth <= $this->lexicon->getMaxDepth())
            ? $depth + 1
            : $depth;

        /** @var $node Node */
        $node = new static;

        $node
            ->setEnvironment($this->lexicon)
            ->getSetup($match);

        return $node
            ->setCount($count)
            ->setDepth($depth)
            ->setId()
            ->setParsedContent($node->getContent())
            ->setParent($parent)
            ->setAttributes();
    }

    /**
     * Set content
     *
     * @return string
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set parsed content
     *
     * @param $parsedContent
     * @return $this
     */
    public function setParsedContent($parsedContent)
    {
        $this->parsedContent = $parsedContent;
        return $this;
    }

    /**
     * Set depth
     *
     * @param $depth
     * @return Node
     */
    public function setDepth($depth = 0)
    {
        $this->depth = $depth;
        return $this;
    }

    /**
     * Get depth
     *
     * @return int
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * @return NodeInterface
     */
    public function setId()
    {
        $this->id = md5($this->getContent() . $this->getName() . $this->getDepth() . $this->getCount());
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setCount($count = 0)
    {
        $this->count = $count;
        return $this;
    }

    public function getCount()
    {
        return $this->count;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setParent(Node $parentNode = null)
    {
        $this->parent = $parentNode;
        return $this;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function isRoot()
    {
        return !$this->parent;
    }

    public function getExtractionId($suffix = null)
    {
        if ($suffix) {
            $suffix .= '__ ';
        }

        return ' __' . get_called_class() . '__' . $this->getName() . '__' . $this->getId() . '__' . $suffix;
    }

    public function getItem()
    {
        $name = str_replace($this->lexicon->getScopeGlue(), ' ', $this->name);

        return str_replace(' ', '', $name) . 'Item';
    }

    public function setAttributes()
    {
        $this->attributes = $this->lexicon->getRegex()->parseAttributes($this->parameters);
        return $this;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getAttribute($name, $default = 0)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        } elseif (isset($this->attributes[$default])) {
            return $this->attributes[$default];
        }

        return null;
    }

    public function setEnvironment(EnvironmentInterface $lexicon)
    {
        $this->lexicon = $lexicon;
        return $this;
    }

    public function getEnvironment()
    {
        return $this->lexicon;
    }

    public function getOpenTagMatches($text)
    {
        return $this->lexicon->getRegex()->getMatches($text, $this->getRegexMatcher());
    }

    public function getSingleTagMatches($text)
    {
        $matches = array();

        /**
         * $data_matches[0] is the raw data tag
         * $data_matches[1] is the data variable (dot notated)
         */
        foreach ($this->getOpenTagMatches($text) as $match) {
            if (!preg_match(
                $this->lexicon->getRegex()->getClosingTagRegexMatcher($this->getName()),
                $text,
                $closingTagMatch
            )
            ) {
                $matches[] = $match;
            }
        }

        return $matches;
    }

    public function createChildNodes()
    {
        $rootNodeType = $this->lexicon->getRootNodeType();

        if ($rootNodeType instanceof Node) {
            $rootNodeType->setEnvironment($this->lexicon);
            foreach ($rootNodeType->getMatches($this->parsedContent) as $count => $match) {
                $this->createChildNode($rootNodeType, $match, $count);
            }
        }

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

        $node->data = [];

        $this->extract($node->createChildNodes());

        return $node;
    }

    protected function extract(Node $node)
    {
        if (method_exists($node, 'compileParentNode')) {
            $this->parsedContent = $node->compileParentNode($this->parsedContent);
        }

        if (!$node->trash) {


            if (method_exists($node, 'getExtractionOpen')) {
                $this->parsedContent = str_replace(
                    $node->getExtractionOpen(),
                    $node->getExtractionId('open'),
                    $this->parsedContent
                );
            }

            $this->parsedContent = str_replace(
                $node->extractionContent,
                $node->getExtractionId(),
                $this->parsedContent
            );

            $this->children[] = $node;

            if (method_exists($node, 'getExtractionClose')) {
                $this->parsedContent = str_replace(
                    $node->getExtractionClose(),
                    $node->getExtractionId('close'),
                    $this->parsedContent
                );
            }
        }

        if ($this->name == 'title') {
            //dd($this->parent->parsedContent);
        }

        return $this;
    }

    protected function inject(Node $node)
    {
        if (method_exists($node, 'compileOpen')) {
            $this->parsedContent = str_replace(
                $node->getExtractionId('open'),
                $node->compileOpen(),
                $this->parsedContent
            );
        }

        $this->parsedContent = str_replace(
            $node->getExtractionId(),
            $node->compile(),
            $this->parsedContent
        );

        if (method_exists($node, 'compileClose')) {
            $this->parsedContent = str_replace(
                $node->getExtractionId('close'),
                $node->compileClose(),
                $this->parsedContent
            );
        }

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

}