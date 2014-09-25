<?php namespace Anomaly\Lexicon\Attribute;

use Anomaly\Lexicon\Contract\Node\NodeInterface;
use Anomaly\Lexicon\Node\Node;

class AttributeNode extends Node
{

    /**
     * The attribute key
     *
     * @var string
     */
    protected $key = '';

    /**
     * The attribute value
     *
     * @var string
     */
    protected $value = '';

    /**
     * @var bool
     */
    protected $extractable = false;

    /**
     * Default dummy regex
     *
     * @return string
     */
    public function regex()
    {
        return '//';
    }

    /**
     * Setup properties using the regex matches
     *
     * @return void
     */
    public function setup()
    {
        $rawAttributes = '';

        if ($parent = $this->getParent()) {
            $this->setParentId($parent->getId());
            $rawAttributes = $parent->getRawAttributes();
        }

        $this
            ->setKey($this->match(1))
            ->setValue($this->match(3))
            ->setContent($rawAttributes)
            ->setCurrentContent($rawAttributes);
    }

    /**
     * Detect if this node type should be used to compile
     *
     * @param $rawAttributes
     * @return bool
     */
    public function detect($rawAttributes)
    {
        return !empty($this->getMatches($rawAttributes));
    }

    /**
     * @todo - update this interface for an attribute specific one
     * @return NodeInterface|null
     */
    public function getAttributeNodeType()
    {
        $attributeNodeType = null;

        foreach ($this->getLexicon()->getNodeFactory()->getAttributeNodeTypes() as $nodeType) {
            if ($nodeType->detect($this->getCurrentContent())) {
                $attributeNodeType = $nodeType;
                break;
            }
        }

        return $attributeNodeType;
    }


    /**
     * Alias for get attribute node types
     *
     * @return array
     */
    public function getNodeTypes()
    {
        return $this->getLexicon()->getNodeFactory()->getAttributeNodeTypes();
    }

    /**
     * Create child nodes
     *
     * @param AttributeNode $nodeType
     * @return NodeInterface
     */
    public function createChildNodes(AttributeNode $nodeType = null)
    {
        if (!$nodeType) {
            $nodeType = $this->getAttributeNodeType();
        }

        /** @var AttributeNode $nodeType */
        if ($nodeType) {
            foreach ($nodeType->getMatches($this->getCurrentContent()) as $offset => $match) {
                $this->createChildNode($nodeType, $match, $offset);
            }
        }

        return $this;
    }

    /**
     * Set the key
     *
     * @param string $key
     * @return AttributeNode
     */
    public function setKey($key = '')
    {
        $this->key = $key;
        return $this;
    }

    /**
     * Get the key
     *
     * @return string
     */
    public function getKey()
    {
        return trim($this->key);
    }

    /**
     * Set the value
     *
     * @param string $value
     * @return AttributeNode
     */
    public function setValue($value = '')
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get the value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Compile key as an a string or offset
     *
     * @return string
     */
    public function compileKey()
    {
        $key = $this->getKey();

        if (!is_numeric($key)) {
            $key = "'{$key}'";
        }

        return $key;
    }

    /**
     * Compile literal value
     *
     * @return string
     */
    public function compileLiteral()
    {
        return "'{$this->getValue()}'";
    }

    /**
     * Compile value
     *
     * @return string
     */
    public function compileValue()
    {
        return $this->compileLiteral();
    }

    /**
     * Compile array
     *
     * @param array $except
     * @return array
     */
    public function compileArray($except = [])
    {
        $attributes = array();

        /** @var $node AttributeNode */
        foreach ($this->getChildren() as $node) {
            $key   = $node->compileKey();
            $value = $node->compileValue();
            if (!in_array($key, $except) or !in_array($key, array_keys($except))) {
                $attributes[$key] = $value;
            }
        }

        return $attributes;
    }

    /**
     * Compile a single attribute
     *
     * @param        $name
     * @param int    $offset
     * @param string $default
     * @return string
     */
    public function compileAttributeValue($name, $offset = 0, $default = null)
    {
        $attributes = $this->compileArray();

        if (isset($attributes[$name])) {
            return $attributes[$name];
        } elseif (isset($attributes[$offset])) {
            return $attributes[$offset];
        }

        return $default;
    }


    /**
     * Compile attributes
     *
     * @return string
     */
    public function compileSourceFromArray($except = [])
    {
        $attributes = [];

        foreach ($this->compileArray($except) as $key => $value) {
            $attributes[] = "{$key} => {$value}";
        }

        if (!empty($attributes)) {
            $attributes = implode(',', $attributes);
        }

        return '[' . $attributes . ']';
    }

    /**
     * Compile source
     *
     * @return string
     */
    public function compile()
    {
        return $this->compileSourceFromArray();
    }

}