<?php namespace Anomaly\Lexicon\Node;

class Single extends Node
{

    /**
     * Regex
     *
     * @return string
     */
    public function regex()
    {
        return "/\{\{\s*({$this->getNameMatcher()})(\s.*?)?\s*(\/)?\}\}/ms";
    }

    /**
     * Setup properties using the regex matches
     *
     * @return void
     */
    public function setup()
    {
        $this
            ->setExtractionContent($content = $this->match(0))
            ->setName($name = $this->match(1))
            ->setRawAttributes($rawAttributes = $this->match(2))
            ->setContent($rawAttributes);
    }
    
}