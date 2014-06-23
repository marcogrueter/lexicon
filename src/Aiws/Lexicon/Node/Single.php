<?php namespace Aiws\Lexicon\Node;

abstract class Single extends Node
{
    public function getRegexMatcher()
    {
        return "/\{\{\s*({$this->getNameMatcher()})(\s.*?)?\s*(\/)?\}\}/ms";
    }

    public function getSetup(array $match)
    {
        $this
            ->setName($match[1])
            ->setParsedAttributes($match[2])
            ->setExtractionContent($match[0]);
    }

    public function getMatches($text)
    {
        return $this->getSingleTagMatches($text);
    }
}