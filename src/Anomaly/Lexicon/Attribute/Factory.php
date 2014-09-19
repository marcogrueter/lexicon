<?php namespace Anomaly\Lexicon\Attribute;

use Anomaly\Lexicon\Contract\Node\ExtractionInterface;
use Anomaly\Lexicon\Contract\Node\NodeInterface;
use Anomaly\Lexicon\Node\Variable;

class Factory
{

    /**
     * The node
     *
     * @var \Anomaly\Lexicon\Contract\Node\NodeInterface
     */
    protected $node;

    /**
     * @var array
     */
    protected $attributeNodeTypes = [
        'Anomaly\Lexicon\Attribute\NamedAttribute',
        'Anomaly\Lexicon\Attribute\OrderedAttribute',
        'Anomaly\Lexicon\Attribute\VariableAttribute',
    ];

    /**
     * The raw attributes
     *
     * @var string
     */
    protected $rawAttributes;

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

    /**
     * @var array
     */
    protected $attributesExtractions = [];

    /**
     * @var array
     */
    protected $attributesOrder = [];

    /**
     * @var array
     */
    protected $compiledAttributes = [];

    /**
     * @var array
     */
    protected $childrenIds = [];

    /**
     * @param NodeInterface $node
     * @param Variable      $variableNode
     */
    public function __construct(NodeInterface $node, Variable $variableNode)
    {
        $this->node          = $node;
        $this->variableNode  = $variableNode;
    }

    /**
     * @return string
     */
    public function getRawAttributes()
    {
        return $this->node->getRawAttributes();
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
        // foreach attribute node type
        // validate when
        /**
         * $attributeNodeType->when(function($this->rawattributes))


         */

        /*        foreach($this->getAttributeNodeTypes() as $nodeType) {
                    if ($nodeType->detected($this->rawAttributes)) {

                        break;
                    }
                }*/

        // Do we have named attributes?
        if (str_contains($this->rawAttributes, '="') or
            str_contains($this->rawAttributes, '={')
        ) {
            $this->createAttributeNodes(new NamedAttributeNode($this->node->getLexicon()));
        } else {
            $this->createAttributeNodes(new OrderedAttributeNode($this->node->getLexicon()));
        }

        $this->createEmbeddedAttributes();

        $this->attributesOrder = explode(' ', trim($this->node->compress($this->rawAttributes)));

        return $this;
    }

    /**
     * @return array
     */
    public function getNodeTypes()
    {
        $nodeTypes = [];
        foreach ($this->attributeNodeTypes as $nodeType) {
            $nodeTypes[$nodeType] = new $nodeType($this->node->getLexicon());
        }
        return $nodeTypes;
    }

    public function createChildNodes()
    {
        $attributeNodeType = $this->getAttributeNodeType();

        $matches = $attributeNodeType->getMatches($this->getRawAttributes());

        foreach($matches as $offset => $match) {
            $node = $attributeNodeType->make($match, $this->node, $depth = 0, $offset);
            $this->addChild($node);
        }

        return $this;
    }

    public function addChild(NodeInterface $node)
    {
        $this->childrenIds[$node->getId()] = $node->getId();
    }

    public function getNodeById($id)
    {
        return $this->getLexicon()->getNodeById($id);
    }

    public function getAttributeNodeType()
    {
        $attributeNodeType = null;

        foreach($this->getNodeTypes() as $nodeType) {
            if ($nodeType->detect($this->getRawAttributes())) {
                $attributeNodeType = $nodeType;
                break;
            }
        }

        return $attributeNodeType;
    }

    public function getNodes()
    {
        $nodes = [];

        foreach($this->childrenIds as $id) {
            $nodes[] = $this->getLexicon()->getNodeById($id);
        }

        return $nodes;
    }

    public function getAttributes()
    {
        $attributes = [];

        foreach($this->getNodes() as $node) {
            $attributes[$node->getKey()] = $node->getValue();
        }

        return $attributes;
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
        $embeddedMatches = $this->node->getEmbeddedMatches($this->rawAttributes);

        foreach ($embeddedMatches as $match) {

            $embeddedAttribute = $this->variableNode->make($match, $this->node->getParent(), $this->node->getDepth());

            $this->extract($embeddedAttribute);
        }

        return $this;
    }

    public function createAttributeNodes(AttributeNode $attributeNodeType)
    {
        $matches = $attributeNodeType->getMatches($this->rawAttributes);

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
        $this->rawAttributes = str_replace(
            trim($extraction->getExtractionContent()),
            $extraction->getId(),
            $this->rawAttributes
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
        return $this->node->getLexicon();
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