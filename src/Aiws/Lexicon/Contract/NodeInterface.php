<?php namespace Aiws\Lexicon\Contract;

interface NodeInterface
{
    public function setEnvironment(EnvironmentInterface $lexicon);

    public function getEnvironment();

    public function make(array $match, $parent = null, $depth = 0, $count = 0);

    public function getSetup(array $match);

    public function getRegexMatcher();

    public function getMatches($text);

    public function createChildNodes();

    public function compile();

    public function setId($id);

    public function getId();

    public function setItemName($itemName);

    /**
     * @return NodeInterface
     */
    public function getParent();

    /**
     * @return NodeInterface
     */
    public function setDepth();

    public function getDepth();

    public function setParsedContent($parsedContent);

}