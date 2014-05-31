<?php namespace Aiws\Lexicon\Contract;

interface NodeInterface
{
    public function setEnvironment(EnvironmentInterface $lexicon);

    public function make(array $match, $parent = null, $depth = 0, $count = 0);

    public function getSetup(array $match);

    public function getRegex();

    public function getMatches($text);

    public function createChildNodes();

    public function compile();
}