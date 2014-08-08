<?php namespace Aiws\Lexicon\Contract;

interface NodeInterface extends ExtractionInterface
{
    /**
     * Get Lexicon environment
     *
     * @param EnvironmentInterface $lexicon
     * @return NodeInterface
     */
    public function setEnvironment(EnvironmentInterface $lexicon);

    /**
     * Get Lexicon environment
     *
     * @return EnvironmentInterface
     */
    public function getEnvironment();

    /**
     * Make a new instance of this object
     *
     * @param array $match
     * @param null  $parent
     * @param int   $depth
     * @param int   $count
     * @return NodeInterface
     */
    public function make(array $match, $parent = null, $depth = 0, $count = 0);

    /**
     * Get setup from regex match
     *
     * @param array $match
     * @return mixed
     */
    public function getSetup(array $match);

    /**
     * Get regex matcher
     *
     * @return string
     */
    public function getRegexMatcher();

    /**
     * Get matches
     *
     * @param $text
     * @return array
     */
    public function getMatches($text);

    /**
     * Create child nodes
     *
     * @return NodeInterface
     */
    public function createChildNodes();

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
     * Get id
     *
     * @return string
     */
    public function getId();

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
     * @param $parsedContent
     * @return NodeInterface
     */
    public function setParsedContent($parsedContent);

    /**
     * Set node validator
     *
     * @param NodeValidatorInterface $validator
     * @return NodeInterface
     */
    public function setValidator(NodeValidatorInterface $validator);

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

}