<?php namespace Anomaly\Lexicon\Node\NodeType;

class IgnoreBlock extends Node
{

    /**
     * Do not compile as PHP
     *
     * @var bool
     */
    protected $isPhp = false;

    /**
     * Defer compile
     *
     * @var bool
     */
    protected $deferCompile = true;

    /**
     * Setup properties using the regex matches
     *
     * @return void
     */
    public function setup()
    {
        $this->setContent($this->match(3));
    }

    /**
     * Get regex matcher
     *
     * @return string
     */
    public function regex()
    {
        return '/\{\{\s*(ignore)(\s.*?)\}\}(.*?)\{\{\s*\/\1\s*\}\}/ms';
    }

    /**
     * Compile content
     *
     * @return string
     */
    public function compile()
    {
        return $this->getContent();
    }

}