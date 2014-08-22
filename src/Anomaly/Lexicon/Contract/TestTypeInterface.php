<?php namespace Anomaly\Lexicon\Contract;

interface TestTypeInterface
{
    /**
     * The type for this group of tests
     *
     * @return mixed
     */
    public function getType();
}