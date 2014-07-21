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
    protected $node;

    protected $regex;

    protected $attributes;

    protected $attributeNodes = [];

    protected $embedded = [];

    public function __construct(NodeInterface $node)
    {
        $this->node         = $node;
        $this->regex        = $node->getEnvironment()->getRegex();
        $this->attributes   = $node->getParsedAttributes();
        $this->variableNode = new Variable();
        $this->lexicon      = $node->getEnvironment();
        $this->variableNode->setEnvironment($node->getEnvironment());
        $this->stringTest = new StringTest();

        $this->parse();
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

    public function getEmbeddedAttribute($id)
    {
        return isset($this->embedded[$id]) ? $this->embedded[$id] : null;
    }

    public function newEmbeddedAttribute(array $match)
    {
        return new EmbeddedAttribute($match);
    }

    public function createEmbeddedAttributes()
    {
        $embeddedMatches = $this->getMatches($this->attributes, $this->getEmbeddedAttributeRegex());

        foreach ($embeddedMatches as $match) {

            $embedded         = $this->newEmbeddedAttribute($match);

            $this->attributes = str_replace(
                $embedded->getOriginal(),
                '"' . $embedded->getId() . '"',
                $this->attributes
            );

            $this->embedded[$embedded->getId()] = $embedded;
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

            $attributeNode->setEmbedded($this->getEmbeddedAttribute($attributeNode->getEmbeddedId()));

            $this->attributeNodes[] = $attributeNode;
        }

        return $this;
    }

    public function getNamedAttributeMatches()
    {

    }

    public function getOrderedAttributeMatches()
    {

    }

    public function getNamedAttributeRegex()
    {

    }

    public function getOrderedAttributeRegex()
    {
        return '';
    }

    public function getEmbeddedAttributeRegex()
    {
        return "/\{\s*({$this->getRegex()->getVariableRegexMatcher()})(\s+.*?)?\s*(\/)?\}/ms";
    }

    public function getParsedAttributes()
    {
        return $this->parsedAttributes;
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

    public function compileAttributes($except = [])
    {
        $attributes = array();

        /** @var $attributeNode AttributeNode */
        foreach($this->getAttributeNodes() as $attributeNode) {
            $key = $attributeNode->compileKey();
            if (!in_array($key, array_keys($except)) or !in_array($key, $except)) {
                $attributes[$key] = $attributeNode->compile();
            }
        }

        return $attributes;
    }

    public function compileSharedAttributes($except = [])
    {
        $attributes = array();

        /** @var $attributeNode AttributeNode */
        foreach($this->getAttributeNodes() as $attributeNode) {
            $key = $attributeNode->compileSharedKey();
            if (!in_array($key, array_keys($except)) or !in_array($key, $except)) {
                $attributes[$key] = $attributeNode->compile();
            }
        }

        return $attributes;
    }

    public function compileAttribute($name, $offset = 0, $default = '')
    {
        $attributes = $this->compileAttributes();

        if (isset($attributes[$name])) {
            return $attributes[$name];
        } elseif (isset($attributes[$offset])) {
            return $attributes[$offset];
        }

        return $default;
    }

    public function compileShared($except = [])
    {
        $source = "[\n";

        /** @var $attributeNode AttributeNode */
        foreach($this->compileSharedAttributes($except) as $key => $value) {
            if (!is_numeric($key)) {
                $key = "'{$key}'";
            }
            $source .= "{$key} => {$value},\n";
        }

        return $source."]";
    }

    public function compile($except = [])
    {
        $source = "[\n";

        /** @var $attributeNode AttributeNode */
        foreach($this->compileAttributes($except) as $key => $value) {
            if (!is_numeric($key)) {
                $key = "'{$key}'";
            }
            $source .= "{$key} => {$value},\n";
        }

        return $source."]";
    }
}