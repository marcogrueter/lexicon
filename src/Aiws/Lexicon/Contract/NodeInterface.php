<?php namespace Aiws\Lexicon\Contract;

interface NodeInterface
{
    public function setEnvironment(EnvironmentInterface $lexicon);

    public function getSetup(array $match);

    public function getRegex();

    public function getMatches($text);

    public function compile();
}