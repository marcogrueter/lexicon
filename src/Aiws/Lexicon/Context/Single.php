<?php namespace Aiws\Lexicon\Context;

abstract class Single extends ContextType
{
    public function getRegex()
    {
        return "/\{\{\s*($this->name)(\s.*?)\}\}/m";
    }

    public function getSetup(array $match)
    {
        $this->name = $match[1];
        $this->parameters = $match[2];
        $this->extractionContent = $match[0];
    }

    public function getMatches($text, $regex = null)
    {
        return $this->getSingleTagMatches($text, $regex);
    }
}