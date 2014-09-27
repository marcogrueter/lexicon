<?php namespace Anomaly\Lexicon\Node\NodeType;

class SingleRemain extends Single
{

    /**
     * Do not compile as PHP
     *
     * @var bool
     */
    protected $isPhp = false;

    /**
     * Delay compilation after non-deferred
     *
     * @var bool
     */
    protected $deferCompile = true;

    /**
     * Compile
     */
    public function compile()
    {
        return $this->getContent();
    }
}