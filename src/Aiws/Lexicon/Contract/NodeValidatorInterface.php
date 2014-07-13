<?php namespace Aiws\Lexicon\Contract;

interface NodeValidatorInterface
{
    /**
     * Is valid for compilation
     *
     * @return bool
     */
    public function isValid();
}