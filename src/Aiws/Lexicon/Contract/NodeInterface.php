<?php namespace Aiws\Lexicon\Contract;

use Aiws\Lexicon\Node\Node;

interface NodeInterface
{
    public function setEnvironment(EnvironmentInterface $lexicon);

    public function make(array $match, $parent = null, $depth = 0, $count = 0);

    public function getSetup(array $match);

    public function getRegexMatcher();

    public function getMatches($text);

    public function createChildNodes();

    public function compile();

    public function setId();

    public function getId();

    /**
     * @return NodeInterface
     */
    public function setDepth();

    public function getDepth();

    public function setParsedContent($parsedContent);

}