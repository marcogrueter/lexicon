<?php namespace Anomaly\Lexicon\Attribute;

use Anomaly\Lexicon\Contract\EnvironmentInterface;
use Anomaly\Lexicon\Contract\NodeInterface;
use Anomaly\Lexicon\Node\Node;
use Anomaly\Lexicon\Node\Variable;

class AttributeNode extends Node
{

    /**
     * @var EmbeddedAttribute
     */
    protected $embeddedAttribute;

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
     * Is this a named attribute?
     *
     * @var bool
     */
    protected $isNamed = true;

    protected $variableNodes = [];

    protected $extractions = [];

    protected $variableNode;

    protected $attributeSegments = [];

    protected $lexicon;

    public function __construct(EnvironmentInterface $lexicon)
    {
        $this->lexicon = $lexicon;
        $this->variableNode = new Variable($lexicon);
    }

    /**
     * Get the regex match setup
     *
     * @param array $match
     * @return mixed|void
     */
    public function getSetup(array $match)
    {
        //dd($match[0]);

        $this
            ->setExtractionContent(isset($match[0]) ? $match[0] : null)
            ->setKey(isset($match[1]) ? $match[1] : null)
            ->setValue(isset($match[3]) ? $match[3] : '');

        $this->parse();
    }

    public function parse()
    {

        $embeddedMatches = $this->getLexicon()->getRegex()->getEmbeddedMatches($this->value);

        foreach($embeddedMatches as $count => $match) {

            $node = $this->variableNode->make(
                $match,
                $this->getParent(),
                $this->getDepth(),
                $count
            );

            $this->value = str_replace(
                trim($node->getExtractionContent()),
                $node->getId(),
                $this->value
            );

            $this->extractions[$node->getId()] = $node;
        }

        $this->attributeSegments = explode("\n", $this->value);
    }

    public function getExtractionIds()
    {
        return array_keys($this->extractions);
    }

    public function getEmbeddedMatches($string)
    {
        return $this->lexicon->getRegex()->getMatches($string, $this->regex());
    }

    /**
     * Regex matcher
     *
     * @param bool $embedded
     * @return string
     */
    public function regex($embedded = false)
    {
        return '/(.*?)\s*=(\'|"|&#?\w+;)(.*?)(?<!\\\\)\2/ms';
    }

    /**
     * Get the array of regex matches
     *
     * @param $string
     * @return array
     */
    public function getMatches($string)
    {
        return $this->lexicon->getRegex()->getMatches($string, $this->regex());
    }

    /**
     * Set the embedded attribute if it exists
     *
     * @param EmbeddedAttribute $embeddedAttribute
     * @return AttributeNode
     */
    public function setEmbeddedAttribute(EmbeddedAttribute $embeddedAttribute = null)
    {
        $this->embeddedAttribute = $embeddedAttribute;
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
        // @todo - Use ValueResolver here
        return trim($this->value);
    }

    /**
     * Get the embedded id which is the same as the extracted value
     *
     * @return string
     */
    public function getEmbeddedId()
    {
        return $this->getValue();
    }

    /**
     * Get the embedded attribute
     *
     * @return EmbeddedAttribute
     */
    public function getEmbeddedAttribute()
    {
        return $this->embeddedAttribute;
    }

    /**
     * Compile key
     *
     * @return string
     */
    public function compileKey()
    {
        return $this->getKey();
    }

    /**
     * Compile a named key from an ordered embedded attribute
     *
     * @return string
     */
    public function compileNamedFromOrderedKey()
    {
        if (!$this->isNamed and $this->getEmbeddedAttribute()) {

            $node = $this->newVariableNode()->make(['name' => $this->getEmbeddedAttribute()->getName()], $this->getParent());

            $finder = $node->getContextFinder();

            return $finder->getName();
        }

        return $this->getKey();
    }

    public function newVariableNode()
    {
        return new Variable($this->getLexicon());
    }

    public function compileEmbedded()
    {
        $node = $this->newVariableNode()->make(['name' => $this->getEmbeddedAttribute()->getName()], $this->getParent());

        $finder = $node->getContextFinder();

        return "\$this->view()->variable({$finder->getItemName()},'{$finder->getName()}', [])";
    }

    public function compileLiteral()
    {
        return "'{$this->getValue()}'";
    }

    public function compile()
    {
        foreach($this->attributeSegments as &$segment) {
            if (isset($this->extractions[$segment])) {
                $node = $this->extractions[$segment];
                /** @var $node NodeInterface */
                $segment = $node->setIsEmbedded(true)->compile();
            } else {
                $segment = "'{$segment}'";
            }
        }

        return implode('.', $this->attributeSegments);
    }
}