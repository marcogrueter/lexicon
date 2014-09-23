<?php namespace Anomaly\Lexicon\Contract\View;

interface CompiledViewInterface
{
    /**
     * Renders the view contents
     *
     * @param $__data
     * @return void
     */
    public function render($__data);

}