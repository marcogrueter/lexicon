<?php namespace Anomaly\Lexicon\Contract\View;

interface CompilerInterface
{

    /**
     * Compile string
     *
     * @param $string
     * @return string
     */
    public function compile($string);

}