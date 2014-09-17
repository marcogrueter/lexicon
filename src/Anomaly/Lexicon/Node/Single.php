<?php namespace Anomaly\Lexicon\Node;

abstract class Single extends Node
{
    public function regex()
    {
        return "/\{\{\s*({$this->getNameMatcher()})(\s.*?)?\s*(\/)?\}\}/ms";
    }

    public function setup(array $match)
    {
        $this
            ->setName($name = isset($match[1]) ? $match[1] : null)
            ->setRawAttributes(isset($match[2]) ? $match[2] : null)
            ->setExtractionContent($content = isset($match[0]) ? $match[0] : null)
            ->setParsedContent($content);
    }
    
}