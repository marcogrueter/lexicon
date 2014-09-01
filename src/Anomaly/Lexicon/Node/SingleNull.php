<?php namespace Anomaly\Lexicon\Node;

abstract class SingleNull extends Single
{
    /**
     * Compile node source
     *
     * @return null
     */
    public function compile()
    {
        return null;
    }
}