<?php namespace Anomaly\Lexicon\Attribute;

use Anomaly\Lexicon\Contract\Node\ExtractionInterface;
use Anomaly\Lexicon\Contract\Node\NodeInterface;
use Anomaly\Lexicon\Node\Variable;

class AttributeParser
{
    /**
     * The node
     *
     * @var \Anomaly\Lexicon\Contract\Node\NodeInterface
     */
    protected $node;

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

    protected $attributesExtractions = [];

    protected $attributesOrder = [];

    protected $compiledAttributes = [];

    public function __construct(NodeInterface $node)
    {
        $this->node         = $node;
        $this->attributes   = $node->getRawAttributes();
        $this->variableNode = new Variable($node->getLexicon());
        $this->lexicon      = $node->getLexicon();
    }


    /**
     * @return $this
     * 1. Extract named or ordered attributes
     * 2. Extract embedded attributes
     * 3. Extract embedded string attributes from named or ordered
     * 4. Explode all hashes
     */
    public function parse()
    {


        // Do we have named attributes?
        if (str_contains($this->attributes, '="') or
            str_contains($this->attributes, '={')
        ) {
            $this->createAttributeNodes(new NamedAttributeNode($this->node->getLexicon()));
        } else {
            $this->createAttributeNodes(new OrderedAttributeNode($this->node->getLexicon()));
        }

        $this->createEmbeddedAttributes();

        $this->attributesOrder = explode(' ', trim($this->node->compress($this->attributes)));

        return $this;
    }

    public function getEmbeddedById($id)
    {
        return isset($this->embeddedAttributes[$id]) ? $this->embeddedAttributes[$id] : null;
    }

    public function newEmbeddedAttribute(array $match)
    {
        return $this->variableNode->make($match);
    }

    public function createEmbeddedAttributes()
    {
        $embeddedMatches = $this->node->getEmbeddedMatches($this->attributes);

        foreach ($embeddedMatches as $match) {

            $embeddedAttribute = $this->variableNode->make($match, $this->node->getParent(), $this->node->getDepth());

            $this->extract($embeddedAttribute);
        }

        return $this;
    }

    public function createAttributeNodes(AttributeNode $attributeNodeType)
    {
        $matches = $attributeNodeType->getMatches($this->attributes);

        foreach ($matches as $count => $match) {

            $attributeNode = $attributeNodeType->make(
                $match,
                $this->node->getParent(),
                $this->node->getDepth(),
                $count
            );

            $this->extract($attributeNode);
        }

        return $this;
    }

    public function extract(ExtractionInterface $extraction)
    {
        $this->attributes = str_replace(
            trim($extraction->getExtractionContent()),
            $extraction->getId(),
            $this->attributes
        );

        $this->attributesExtractions[$extraction->getId()] = $extraction;
    }

    public function getEmbeddedAttributeRegex()
    {
        return $this->node->getEmbeddedAttributeRegex();
    }

    public function getMatches($string, $regex)
    {
        return $this->node->getMatches($string, $regex);
    }

    public function getLexicon()
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
        foreach ($this->attributesOrder as $count => $id) {
            if (isset($this->attributesExtractions[$id])) {
                $attributeNode = $this->attributesExtractions[$id];
                $key           = $attributeNode->setIsEmbedded(true)->compileKey();
                if (!in_array($key, array_keys($except)) or !in_array($key, $except)) {
                    $attributes[$key] = $attributeNode->compile();
                }
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
        foreach ($this->attributesOrder as $count => $id) {
            if (isset($this->attributesExtractions[$id])) {
                $attributeNode = $this->attributesExtractions[$id];
                $key           = $attributeNode->compileNamedFromOrderedKey();
                if (!in_array($key, array_keys($except)) or !in_array($key, $except)) {
                    $attributes[$key] = $attributeNode->compile();
                }
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