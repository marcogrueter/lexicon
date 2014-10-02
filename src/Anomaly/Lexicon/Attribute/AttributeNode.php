<?php namespace Anomaly\Lexicon\Attribute;

use Anomaly\Lexicon\Contract\Node\NodeInterface;
use Anomaly\Lexicon\Node\NodeType\Node;
use Anomaly\Lexicon\Node\NodeType\Variable;
use Anomaly\Lexicon\Stub\LexiconStub;

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
        $matches = $this->getMatches($rawAttributes);
        return !empty($matches);
    }

    /**
     * @return array
     */
    public function getAttributeNodeTypes()
    {
        return $this->getNodeFactory()->getAttributeNodeTypes();
    }

    /**
     * @todo - update this interface for an attribute specific one
     * @return NodeInterface|null
     */
    public function getAttributeNodeType()
    {
        $attributeNodeType = null;
        foreach ($this->getAttributeNodeTypes() as $nodeType) {
            if ($nodeType->detect($this->getContent())) {
                $attributeNodeType = $nodeType;
                break;
            }
        }
        return $attributeNodeType;
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
            $matches = $nodeType->getMatches($this->getContent());
            foreach ($matches as $offset => $match) {
                $this->createChildNode($nodeType, $match, $offset);
            }
        }

        return $this;
    }

    /**
     * @param NodeInterface $nodeType
     * @param array         $match
     * @param int           $offset
     * @return $this
     */
    public function createChildNode(NodeInterface $nodeType, array $match, $offset = 0)
    {
        $attributeNode = $this->getNodeFactory()->make($nodeType, $match, $this, $offset);
        $this->addChild($attributeNode);
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
        $attributes = [];
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
        $name = "'" . $name . "'";

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
            $attributes[] = "{$key}=>{$value}";
        }
        return '[' . implode(',', $attributes) . ']';
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

    /**
     * Lexicon
     *
     * @return AttributeNode
     */
    public static function stub()
    {
        $lexicon      = LexiconStub::get();
        $nodeFactory  = $lexicon->getFoundation()->getNodeFactory();
        $variableNode = $nodeFactory->make(new Variable($lexicon));
        $variableNode->setName('example');
        return $nodeFactory->make(new static($lexicon), [], $variableNode);
    }

}