<?php namespace Anomaly\Lexicon\Stub\Node;

use Anomaly\Lexicon\Attribute\AttributeNode;
use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\Node\BlockInterface;
use Anomaly\Lexicon\Contract\Node\NodeInterface;
use Anomaly\Lexicon\Contract\Node\RootInterface;
use Anomaly\Lexicon\Contract\Node\ValidatorInterface;
use Anomaly\Lexicon\Lexicon;
use Anomaly\Lexicon\Node\NodeFinder;

/**
 * Class Root
 *
 * @package Anomaly\Lexicon\Stub\Node
 */
class Root implements RootInterface
{

    /**
     * Set full content
     *
     * @param $fullContent
     * @return BlockInterface
     */
    public function setFullContent($fullContent)
    {
        // TODO: Implement setFullContent() method.
    }

    /**
     * Get full content
     *
     * @return string
     */
    public function getFullContent()
    {
        // TODO: Implement getFullContent() method.
    }

    /**
     * Set content open
     *
     * @param $contentOpen
     * @return BlockInterface
     */
    public function setOpeningTag($contentOpen)
    {
        // TODO: Implement setOpeningTag() method.
    }

    /**
     * Set content close
     *
     * @param $contentClose
     * @return BlockInterface
     */
    public function setClosingTag($contentClose)
    {
        // TODO: Implement setClosingTag() method.
    }

    /**
     * Get opening tag
     *
     * @return string
     */
    public function getOpeningTag()
    {
        // TODO: Implement getOpeningTag() method.
    }

    /**
     * Get closing tag
     *
     * @return string
     */
    public function getClosingTag()
    {
        // TODO: Implement getClosingTag() method.
    }

    /**
     * Compile opening source
     *
     * @return string
     */
    public function compileOpeningTag()
    {
        // TODO: Implement compileOpeningTag() method.
    }

    /**
     * Compile closing source
     *
     * @return string
     */
    public function compileClosingTag()
    {
        // TODO: Implement compileClosingTag() method.
    }

    /**
     * @return array|\IteratorAggregate
     */
    public function getIterateableSource()
    {
        // TODO: Implement getIterateableSource() method.
    }

    /**
     * Get the original content
     *
     * @return string
     */
    public function getContent()
    {
        // TODO: Implement getContent() method.
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        // TODO: Implement getId() method.
    }

    /**
     * Get Lexicon environment
     *
     * @return LexiconInterface
     */
    public function getLexicon()
    {
        // TODO: Implement getLexicon() method.
    }

    /**
     * Get setup from regex match
     *
     * @return mixed
     */
    public function setup()
    {
        // TODO: Implement setup() method.
    }

    /**
     * Get regex string
     *
     * @return string
     */
    public function regex()
    {
        // TODO: Implement regex() method.
    }

    /**
     * Compile source
     *
     * @return string
     */
    public function compile()
    {
        // TODO: Implement compile() method.
    }

    /**
     * @param $id
     * @return NodeInterface
     */
    public function setId($id)
    {
        // TODO: Implement setId() method.
    }

    /**
     * Get the node name
     *
     * @return string
     */
    public function getName()
    {
        // TODO: Implement getName() method.
    }

    /**
     * Set the node name
     *
     * @return NodeInterface
     */
    public function setName($name)
    {
        // TODO: Implement setName() method.
    }

    /**
     * Set item name
     *
     * @param $itemName string
     * @return NodeInterface
     */
    public function setItemName($itemName)
    {
        // TODO: Implement setItemName() method.
    }

    /**
     * @return NodeInterface
     */
    public function getParent()
    {
        // TODO: Implement getParent() method.
    }

    /**
     * Get the child nodes
     *
     * @return array
     */
    public function getChildren()
    {
        // TODO: Implement getChildren() method.
    }

    /**
     * @return NodeInterface
     */
    public function setDepth()
    {
        // TODO: Implement setDepth() method.
    }

    /**
     * Get depth
     *
     * @return int
     */
    public function getDepth()
    {
        // TODO: Implement getDepth() method.
    }

    /**
     * Set offset
     *
     * @param int $count
     * @return NodeInterface
     */
    public function setOffset($count = 0)
    {
        // TODO: Implement setOffset() method.
    }

    /**
     * Set parent id
     *
     * @param null $parentId
     * @return NodeInterface
     */
    public function setParentId($parentId = null)
    {
        // TODO: Implement setParentId() method.
    }

    /**
     * Set parsed content
     *
     * @param $parsedContent
     * @return NodeInterface
     */
    public function setCurrentContent($parsedContent)
    {
        // TODO: Implement setCurrentContent() method.
    }

    /**
     * Get parsed content
     *
     * @return string
     */
    public function getCurrentContent()
    {
        // TODO: Implement getCurrentContent() method.
    }

    /**
     * Set node validator
     *
     * @param ValidatorInterface $validator
     * @return NodeInterface
     */
    public function setValidator(ValidatorInterface $validator)
    {
        // TODO: Implement setValidator() method.
    }

    /**
     * Get node validator
     *
     * @return ValidatorInterface
     */
    public function getValidator()
    {
        // TODO: Implement getValidator() method.
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        // TODO: Implement isValid() method.
    }

    /**
     * Validate node for compilation
     *
     * @return bool
     */
    public function validate()
    {
        // TODO: Implement validate() method.
    }

    /**
     * @return string
     */
    public function getVariableRegex()
    {
        // TODO: Implement getVariableRegex() method.
    }

    /**
     * @param      $string
     * @param null $regex
     * @return array
     */
    public function getMatches($string, $regex = null)
    {
        // TODO: Implement getMatches() method.
    }

    /**
     * Get match
     *
     * @param $string
     * @param $regex
     * @return array
     */
    public function getSingleMatch($string, $regex)
    {
        // TODO: Implement getSingleMatch() method.
    }

    /**
     * Defer compile
     *
     * @return bool
     */
    public function deferCompile()
    {
        // TODO: Implement deferCompile() method.
    }

    /**
     * Set extraction content
     *
     * @param $content
     * @return NodeInterface
     */
    public function setExtractionContent($content)
    {
        // TODO: Implement setExtractionContent() method.
    }

    /**
     * Get extraction content
     *
     * @return string
     */
    public function getExtractionContent()
    {
        // TODO: Implement getExtractionContent() method.
    }

    /**
     * Get extraction id
     *
     * @return string
     */
    public function getExtractionId()
    {
        // TODO: Implement getExtractionId() method.
    }

    /**
     * Set content
     *
     * @param $content
     * @return NodeInterface
     */
    public function setContent($content)
    {
        // TODO: Implement setContent() method.
    }

    /**
     * Get item source
     *
     * @return string
     */
    public function getItemSource()
    {
        // TODO: Implement getItemSource() method.
    }

    /**
     * Get loop item name
     *
     * @return string
     */
    public function getLoopItemName()
    {
        // TODO: Implement getLoopItemName() method.
    }

    /**
     * Set node set
     *
     * @param string $nodeSet
     * @return LexiconInterface
     */
    public function setNodeSet($nodeSet = Lexicon::DEFAULT_NODE_SET)
    {
        // TODO: Implement setNodeSet() method.
    }

    /**
     * Get node set
     *
     * @return string
     */
    public function getNodeSet()
    {
        // TODO: Implement getNodeSet() method.
    }

    /**
     * The raw attributes string
     *
     * @return string
     */
    public function getRawAttributes()
    {
        // TODO: Implement getRawAttributes() method.
    }

    /**
     * Set parsed attributes
     *
     * @param $rawAttributes
     * @return NodeInterface
     */
    public function setRawAttributes($rawAttributes)
    {
        // TODO: Implement setRawAttributes() method.
    }

    /**
     * Set loop item name
     *
     * @param $loopItemName
     * @return mixed
     */
    public function setLoopItemName($loopItemName)
    {
        // TODO: Implement setLoopItemName() method.
    }

    /**
     * Get loop item in raw attributes
     *
     * @return string
     */
    public function getLoopItemInRawAttributes()
    {
        // TODO: Implement getLoopItemInRawAttributes() method.
    }

    /**
     * @return bool
     */
    public function isFilter()
    {
        // TODO: Implement isFilter() method.
    }

    /**
     * @return bool
     */
    public function isParse()
    {
        // TODO: Implement isParse() method.
    }

    /**
     * Should compile to PHP?
     *
     * @return bool
     */
    public function isPhp()
    {
        // TODO: Implement isPhp() method.
    }

    /**
     * @return NodeFinder
     */
    public function getNodeFinder()
    {
        // TODO: Implement getNodeFinder() method.
    }

    /**
     * @return AttributeNode
     */
    public function getAttributes()
    {
        // TODO: Implement getAttributes() method.
    }

    /**
     * Set attribute node
     *
     * @param $attributeNode
     * @return NodeInterface
     */
    public function setAttributeNode(AttributeNode $attributeNode)
    {
        // TODO: Implement setAttributeNode() method.
    }

    /**
     * Set node finder
     *
     * @param $param
     * @return NodeInterface
     */
    public function setNodeFinder(NodeFinder $nodeFinder)
    {
        // TODO: Implement setNodeFinder() method.
    }

    /**
     * @param $match
     * @return NodeInterface
     */
    public function setMatch(array $match)
    {
        // TODO: Implement setMatch() method.
    }

    /**
     * @return bool
     */
    public function incrementDepth()
    {
        // TODO: Implement incrementDepth() method.
    }

    /**
     * Get footer
     *
     * @return array
     */
    public function getFooter()
    {
        // TODO: Implement getFooter() method.
    }

    /**
     * Compile footer
     *
     * @param $source
     * @return mixed|string
     */
    public function compileFooter($source)
    {
        // TODO: Implement compileFooter() method.
    }

    /**
     * Add to footer
     *
     * @param $content
     * @return BlockInterface
     */
    public function addToFooter($content)
    {
        // TODO: Implement addToFooter() method.
    }
}