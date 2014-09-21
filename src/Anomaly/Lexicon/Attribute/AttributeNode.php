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
            ->setParsedContent($rawAttributes);
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

        foreach ($this->getLexicon()->getAttributeNodeTypes() as $nodeType) {
            if ($nodeType->detect($this->getParsedContent())) {
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
        return $this->getLexicon()->getAttributeNodeTypes();
    }

    /**
     * Create child nodes
     *
     * @return NodeInterface
     */
    public function createChildNodes()
    {
        /** @var NodeInterface $nodeType */
        if ($nodeType = $this->getAttributeNodeType()) {
            foreach ($nodeType->getMatches($this->getParsedContent()) as $offset => $match) {
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
     * Compile a named key from an ordered embedded attribute
     *
     * @return string
     */
    /*    public function compileNamedFromOrderedKey()
        {
            if (!$this->isNamed and $this->getEmbeddedAttribute()) {

                $node = $this->newVariableNode()->make([], $this->getParent())
                    ->setName($this->getEmbeddedAttribute()->getName());

                $finder = $node->getNodeFinder();

                return $finder->getName();
            }

            return $this->getKey();
        }*/

    public function compileEmbedded()
    {
        $finder = $this->getNodeFinder();

        return "\$__data['__env']->variable({$finder->getItemSource()},'{$finder->getName()}', [])";
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
            if (!in_array($key, array_keys($except)) or !in_array($key, $except)) {
                $attributes[$key] = $value;
            }
        }

        return $attributes;
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
     * Compile attributes
     *
     * @return string
     */
    public function compile()
    {
        $attributes = [];

        /** @var $node AttributeNode */
        foreach ($this->getChildren() as $node) {
            $attributes[] = "{$node->compileKey()} => {$node->compileValue()}";
        }

        $attributes = implode(', ', $attributes);

        return "[{$attributes}]";
    }

}