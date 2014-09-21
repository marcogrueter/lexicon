<?php namespace Anomaly\Lexicon\Contract\Node;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Lexicon;

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
     * Make a new instance of this object
     *
     * @param array $match
     * @param null  $parentId
     * @param int   $depth
     * @param int   $count
     * @internal param null $parent
     * @return NodeInterface
     */
    public function make(array $match, NodeInterface $parent = null, $depth = 0, $count = 0);

    /**
     * Create child nodes
     *
     * @return NodeInterface
     */
    public function createChildNodes();

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
    public function setParsedContent($parsedContent);

    /**
     * Get parsed content
     *
     * @return string
     */
    public function getParsedContent();

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
     * Set context name
     *
     * @param $contextName
     * @return NodeInterface
     */
    public function setContextName($contextName);

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
     * Get context name
     *
     * @return string
     */
    public function getContextName();

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

}