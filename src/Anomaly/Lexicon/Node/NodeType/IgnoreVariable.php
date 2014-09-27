<?php namespace Anomaly\Lexicon\Node\NodeType;

class IgnoreVariable extends SingleRemain
{

    /**
     * Do not compile as PHP
     *
     * @var bool
     */
    protected $isPhp = false;

    /**
     * Setup properties using the regex matches
     *
     * @return void
     */
    public function setup()
    {
        $this
            ->setExtractionContent($this->match(0))
            ->setContent($this->match(1))
            ->setName($this->match(2));
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