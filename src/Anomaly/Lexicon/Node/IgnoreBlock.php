<?php namespace Anomaly\Lexicon\Node;

class IgnoreBlock extends Block
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