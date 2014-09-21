<?php namespace Anomaly\Lexicon\Attribute;

use Anomaly\Lexicon\Contract\Node\NodeInterface;
use Anomaly\Lexicon\Node\Node;
use Anomaly\Lexicon\Node\NodeManageable;
use Anomaly\Lexicon\Support\ValueResolver;

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
     * Get the regex match setup
     *
     * @param array $match
     * @return mixed|void
     */
    public function setup(array $match)
    {
        $rawAttributes = '';

        if ($parent = $this->getParent()) {
            $this->setParentId($parent->getId());
            $rawAttributes = $parent->getRawAttributes();
        }

        $this
            ->setContent($rawAttributes)
            ->setParsedContent($rawAttributes)
            ->setKey($this->get($match, 1))
            ->setValue($this->get($match, 3));
    }

    /**
     * Regex
     *
     * @return string
     */
    public function regex()
    {
        return '//ms';
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

    public function compileLiteral()
    {
        return "'{$this->getValue()}'";
    }

    public function compileArray($except = [], $qu)
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

    public function compileValue()
    {
        return $this->compileLiteral();
    }

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