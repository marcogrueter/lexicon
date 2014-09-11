<?php namespace Anomaly\Lexicon\Contract\Node;

use Anomaly\Lexicon\Contract\LexiconInterface;

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
     * @param array $match
     * @return mixed
     */
    public function setup(array $match);

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
     * @param null  $parent
     * @param int   $depth
     * @param int   $count
     * @return NodeInterface
     */
    public function make(array $match, $parentId = null, $depth = 0, $count = 0);

    /**
     * Create child nodes
     *
     * @return NodeInterface
     */
    public function createChildNodes();

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
     * Set count
     *
     * @param int $count
     * @return NodeInterface
     */
    public function setCount($count = 0);

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
     * @param NodeValidatorInterface $validator
     * @return NodeInterface
     */
    public function setValidator(ValidatorInterface $validator);

    /**
     * Get node validator
     *
     * @return NodeValidatorInterface
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

}