<?php namespace Aiws\Lexicon\Node;

abstract class Single extends Node
{
    public function getRegexMatcher()
    {
        return "/\{\{\s*({$this->getName()})(\s.*?)?\s*(\/)?\}\}/ms";
    }

    public function getSetup(array $match)
    {
        $this->name = $match[1];
        $this->parameters = $match[2];
        $this->extractionContent = $match[0];
    }

    public function getMatches($text)
    {
        return $this->getSingleTagMatches($text);
    }
}