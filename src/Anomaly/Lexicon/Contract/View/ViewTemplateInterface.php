<?php namespace Anomaly\Lexicon\Contract\View;

interface ViewTemplateInterface
{
    /**
     * Renders the view contents
     *
     * @param $__data
     * @return void
     */
    public function render($__data);
}