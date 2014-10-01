<?php namespace Anomaly\Lexicon\Contract\Node;

use Anomaly\Lexicon\Attribute\AttributeNode;
use Anomaly\Lexicon\Contract\LexiconInterface;
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
    public function getItemAlias();

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
     * Set item alias
     *
     * @param $loopItemName
     * @return mixed
     */
    public function setItemAlias($itemAlias);

    /**
     * Get loop item in raw attributes
     *
     * @return string
     */
    public function getItemAliasFromRawAttributes();

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
     * Is this the root node?
     *
     * @return bool
     */
    public function isRoot();

    /**
     * Is the node extractable
     *
     * @return bool
     */
    public function isExtractable();

    /**
     * Get node finder
     *
     * @return NodeFinder
     */
    public function getNodeFinder();

    /**
     * Get attribute node
     *
     * @return AttributeNode
     */
    public function getAttributeNode();

    /**
     * Set attribute node
     *
     * @param $attributeNode
     * @return NodeInterface
     */
    public function setAttributeNode(AttributeNode $attributeNode);

    /**
     * @param $match
     * @return NodeInterface
     */
    public function setMatch(array $match);

    /**
     * @return bool
     */
    public function incrementDepth();

    /**
     * Add child node
     *
     * @param $node
     * @return NodeInterface
     */
    public function addChild(NodeInterface $node);

    /**
     * Get siblings
     *
     * @return array
     */
    public function getSiblings();

    /**
     * Get first sibling
     *
     * @return NodeInterface
     */
    public function getFirstSibling();

    /**
     * Get position within content
     *
     * @return int
     */
    public function getPosition();

}