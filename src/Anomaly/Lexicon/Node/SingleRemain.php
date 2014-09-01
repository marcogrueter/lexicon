<?php namespace Anomaly\Lexicon\Node;

abstract class SingleRemain extends Single
{
    /**
     * Compile string
     *
     * @return string
     */
    public function compile()
    {
        return '{{ ' . $this->getName() . ' }}';
    }
}