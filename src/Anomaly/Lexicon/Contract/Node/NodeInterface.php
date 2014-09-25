<?php namespace Anomaly\Lexicon\Contract\Node;

use Anomaly\Lexicon\Attribute\AttributeNode;
use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Lexicon;
use Anomaly\Lexicon\Node\NodeFinder;

interface NodeInterface extends ExtractionInterface
{

    /**
     * Get Lexicon environment
     *
     * @return LexiconInterface
     */
    public function getLexicon();

    /**
     * Get setup from regex match
     *
     * @return mixed
     */
    public function setup();

    /**
     * Get regex string
     *
     * @return string
     */
    public function regex();

    /**
     * Compile source
     *
     * @return string
     */
    public function compile();

    /**
     * @param $id
     * @return NodeInterface
     */
    public function setId($id);

    /**
     * Get the node name
     *
     * @return string
     */
    public function getName();

    /**
     * Set the node name
     *
     * @return NodeInterface
     */
    public function setName($name);

    /**
     * Set item name
     *
     * @param $itemName string
     * @return NodeInterface
     */
    public function setItemName($itemName);

    /**
     * @return NodeInterface
     */
    public function getParent();

    /**
     * Get the child nodes
     *
     * @return array
     */
    public function getChildren();

    /**
     * @return NodeInterface
     */
    public function setDepth();

    /**
     * Get depth
     *
     * @return int
     */
    public function getDepth();

    /**
     * Set offset
     *
     * @param int $count
     * @return NodeInterface
     */
    public function setOffset($count = 0);

    /**
     * Set parent id
     *
     * @param null $parentId
     * @return NodeInterface
     */
    public function setParentId($parentId = null);

    /**
     * Set parsed content
     *
     * @param $parsedContent
     * @return NodeInterface
     */
    public function setCurrentContent($parsedContent);

    /**
     * Get parsed content
     *
     * @return string
     */
    public function getCurrentContent();

    /**
     * Set node validator
     *
     * @param ValidatorInterface $validator
     * @return NodeInterface
     */
    public function setValidator(ValidatorInterface $validator);

    /**
     * Get node validator
     *
     * @return ValidatorInterface
     */
    public function getValidator();

    /**
     * @return bool
     */
    public function isValid();

    /**
     * Validate node for compilation
     *
     * @return bool
     */
    public function validate();

    /**
     * @return string
     */
    public function getVariableRegex();

    /**
     * @param      $string
     * @param null $regex
     * @return array
     */
    public function getMatches($string, $regex = null);

    /**
     * Get match
     *
     * @param $string
     * @param $regex
     * @return array
     */
    public function getSingleMatch($string, $regex);

    /**
     * Defer compile
     *
     * @return bool
     */
    public function deferCompile();

    /**
     * Set extraction content
     *
     * @param $content
     * @return NodeInterface
     */
    public function setExtractionContent($content);

    /**
     * Get extraction content
     *
     * @return string
     */
    public function getExtractionContent();

    /**
     * Get extraction id
     *
     * @return string
     */
    public function getExtractionId();

    /**
     * Set content
     *
     * @param $content
     * @return NodeInterface
     */
    public function setContent($content);

    /**
     * Get item source
     *
     * @return string
     */
    public function getItemSource();

    /**
     * Get loop item name
     *
     * @return string
     */
    public function getLoopItemName();

    /**
     * Set node set
     *
     * @param string $nodeSet
     * @return LexiconInterface
     */
    public function setNodeSet($nodeSet = Lexicon::DEFAULT_NODE_SET);

    /**
     * Get node set
     *
     * @return string
     */
    public function getNodeSet();

    /**
     * The raw attributes string
     *
     * @return string
     */
    public function getRawAttributes();

    /**
     * Set parsed attributes
     *
     * @param $rawAttributes
     * @return NodeInterface
     */
    public function setRawAttributes($rawAttributes);

    /**
     * Set loop item name
     *
     * @param $loopItemName
     * @return mixed
     */
    public function setLoopItemName($loopItemName);

    /**
     * Get loop item in raw attributes
     *
     * @return string
     */
    public function getLoopItemInRawAttributes();

    /**
     * @return bool
     */
    public function isFilter();

    /**
     * @return bool
     */
    public function isParse();

    /**
     * Should compile to PHP?
     *
     * @return bool
     */
    public function isPhp();

    /**
     * @return NodeFinder
     */
    public function getNodeFinder();

    /**
     * @return AttributeNode
     */
    public function getAttributes();

    /**
     * Set attribute node
     *
     * @param $attributeNode
     * @return NodeInterface
     */
    public function setAttributeNode(AttributeNode $attributeNode);

    /**
     * Set node finder
     *
     * @param $param
     * @return NodeInterface
     */
    public function setNodeFinder(NodeFinder $nodeFinder);

    /**
     * @param $match
     * @return NodeInterface
     */
    public function setMatch(array $match);

    /**
     * @return bool
     */
    public function incrementDepth();

}