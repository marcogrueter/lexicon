<?php namespace Anomaly\Lexicon\Contract\Node;

interface ValidatorInterface
{
    /**
     * Is valid for compilation
     *
     * @return bool
     */
    public function isValid();
}