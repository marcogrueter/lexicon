<?php namespace Anomaly\Lexicon\Node;

abstract class Single extends Node
{
    public function regex()
    {
        return "/\{\{\s*({$this->getNameMatcher()})(\s.*?)?\s*(\/)?\}\}/ms";
    }

    public function setup(array $match)
    {
        $name = isset($match[1]) ? $match[1] : null;

        $name = isset($match['name']) ? $match['name'] : $name;

        $parsedAttributes = isset($match[2]) ? $match[2] : null;

        $parsedAttributes = isset($match['attributes']) ? $match['attributes'] : $parsedAttributes;

        $extractionContent = isset($match[0]) ? $match[0] : null;

        $extractionContent = isset($match['content']) ? $match['content'] : $extractionContent;

        $this
            ->setName($name)
            ->setParsedAttributes($parsedAttributes)
            ->setExtractionContent($extractionContent);
    }
    
}