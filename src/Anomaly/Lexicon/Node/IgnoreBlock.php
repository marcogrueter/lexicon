<?php namespace Anomaly\Lexicon\Node;

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
     * No is setup needed as we are going to output the original template content
     *
     * @codeCoverageIgnore
     * @param array $match
     * @return void
     */
    public function setup(array $match)
    {
        $this->setContent(isset($match[3]) ? $match[3] : null);
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