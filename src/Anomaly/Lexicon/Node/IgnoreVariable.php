<?php namespace Anomaly\Lexicon\Node;

class IgnoreVariable extends SingleRemain
{

    /**
     * Do not compile as PHP
     *
     * @var bool
     */
    protected $isPhp = false;

    /**
     * Setup
     *
     * @param array $match
     */
    public function setup(array $match)
    {
        $this
            ->setName(isset($match[2]) ? $match[2] : null)
            ->setContent(isset($match[1]) ? $match[1] : null)
            ->setExtractionContent(isset($match[0]) ? $match[0] : null);
    }

    /**
     * Regex
     *
     * @return string
     */
    public function regex()
    {
        return '/@(\{\{\s*(.*?)(\s.*?)?\s*(\/)?\}\})/ms';
    }

}