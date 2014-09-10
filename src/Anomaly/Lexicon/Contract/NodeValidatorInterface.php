<?php namespace Anomaly\Lexicon\Contract;

interface NodeValidatorInterface
{
    /**
     * Is valid for compilation
     *
     * @return bool
     */
    public function isValid();
}