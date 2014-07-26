<?php namespace Aiws\Lexicon\Util;

use Aiws\Lexicon\Contract\NodeInterface;
use Aiws\Lexicon\Node\Variable;
use Aiws\Lexicon\Util\Attribute\AttributeNode;
use Aiws\Lexicon\Util\Attribute\EmbeddedAttribute;
use Aiws\Lexicon\Util\Attribute\NamedAttribute;
use Aiws\Lexicon\Util\Attribute\NamedAttributeNode;
use Aiws\Lexicon\Util\Attribute\OrderedAttributeNode;
use Aiws\Lexicon\Util\Conditional\Test\StringTest;

class AttributeParser
{
    /**
     * The node
     *
     * @var \Aiws\Lexicon\Contract\NodeInterface
     */
    protected $node;

    /**
     * Regex util
     *
     * @var Regex
     */
    protected $regex;

    /**
     * The raw attributes
     *
     * @var string
     */
    protected $attributes;

    /**
     * Array of attribute nodes
     *
     * @var array
     */
    protected $attributeNodes = [];

    /**
     * @var array
     */
    protected $embeddedAttributes = [];

    public function __construct(NodeInterface $node)
    {
        $this->node         = $node;
        $this->regex        = $node->getEnvironment()->getRegex();
        $this->attributes   = $node->getParsedAttributes();
        $this->variableNode = new Variable();
        $this->lexicon      = $node->getEnvironment();
        $this->variableNode->setEnvironment($node->getEnvironment());
        $this->stringTest = new StringTest();
    }

    public function parse()
    {
        $this->createEmbeddedAttributes();

        // Do we have named attributes?
        if ($this->stringTest->contains($this->attributes, '="') or
            $this->stringTest->contains($this->attributes, '={')
        ) {
            $this->createAttributeNodes(new NamedAttributeNode());
        } else {
            $this->createAttributeNodes(new OrderedAttributeNode());
        }

        return $this;
    }

    public function getEmbeddedById($id)
    {
        return isset($this->embeddedAttributes[$id]) ? $this->embeddedAttributes[$id] : null;
    }

    public function newEmbeddedAttribute(array $match)
    {
        return new EmbeddedAttribute($match);
    }

    public function createEmbeddedAttributes()
    {
        $embeddedMatches = $this->getMatches($this->attributes, $this->getEmbeddedAttributeRegex());

        foreach ($embeddedMatches as $match) {

            $embeddedAttribute = $this->newEmbeddedAttribute($match);

            $this->attributes = str_replace(
                $embeddedAttribute->getOriginal(),
                '"' . $embeddedAttribute->getId() . '"',
                $this->attributes
            );

            $this->embeddedAttributes[$embeddedAttribute->getId()] = $embeddedAttribute;
        }

        return $this;
    }

    public function createAttributeNodes(AttributeNode $attributeNodeType)
    {
        $attributeNodeType->setEnvironment($this->node->getEnvironment());

        $matches = $attributeNodeType->getMatches($this->attributes);

        foreach ($matches as $count => $match) {

            $attributeNode = $attributeNodeType->make(
                $match,
                $this->node->getParent(),
                $this->node->getDepth(),
                $count
            );

            $attributeNode->setEmbeddedAttribute($this->getEmbeddedById($attributeNode->getEmbeddedId()));

            $this->attributeNodes[] = $attributeNode;
        }

        return $this;
    }

    public function getEmbeddedAttributeRegex()
    {
        return "/\{\s*({$this->getRegex()->getVariableRegexMatcher()})(\s+.*?)?\s*(\/)?\}/ms";
    }

    public function getMatches($string, $regex)
    {
        return $this->getRegex()->getMatches($string, $regex);
    }

    public function getRegex()
    {
        return $this->getEnvironment()->getRegex();
    }

    public function getEnvironment()
    {
        return $this->lexicon;
    }

    public function getAttributeNodes()
    {
        return $this->attributeNodes;
    }

    public function compileArray($except = [])
    {
        $attributes = array();

        /** @var $attributeNode AttributeNode */
        foreach ($this->getAttributeNodes() as $attributeNode) {
            $key = $attributeNode->compileKey();
            if (!in_array($key, array_keys($except)) or !in_array($key, $except)) {
                $attributes[$key] = $attributeNode->compile();
            }
        }

        return $attributes;
    }

    /**
     * Compiled named array from ordered
     *
     * @param array $except
     * @return array
     */
    public function compileNamedFromOrderedArray($except = [])
    {
        $attributes = array();

        /** @var $attributeNode AttributeNode */
        foreach ($this->getAttributeNodes() as $attributeNode) {
            $key = $attributeNode->compileNamedFromOrderedKey();
            if (!in_array($key, array_keys($except)) or !in_array($key, $except)) {
                $attributes[$key] = $attributeNode->compile();
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
    public function compileAttribute($name, $offset = 0, $default = '')
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
     * Compile named from ordered attributes
     *
     * @param array $except
     * @return string
     */
    public function compileNamedFromOrdered($except = [])
    {
        $source = "[";

        /** @var $attributeNode AttributeNode */
        foreach ($this->compileNamedFromOrderedArray($except) as $key => $value) {
            if (!is_numeric($key)) {
                $key = "'{$key}'";
            }
            $source .= "{$key} => {$value},";
        }

        return $source . "]";
    }

    /**
     * Compile attributes
     *
     * @param array $except
     * @return string
     */
    public function compile($except = [])
    {
        $source = "[";

        /** @var $attributeNode AttributeNode */
        foreach ($this->compileArray($except) as $key => $value) {
            if (!is_numeric($key)) {
                $key = "'{$key}'";
            }
            $source .= "{$key} => {$value},";
        }

        return $source . "]";
    }
}