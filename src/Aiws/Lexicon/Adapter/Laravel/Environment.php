<?php namespace Aiws\Lexicon\Adapter\Laravel;

use Illuminate\View\Environment as BaseEnvironment;

class Environment extends BaseEnvironment
{
    public function make($view, $data = array(), $mergeData = array())
    {
        $view = parent::make($view, $data, $mergeData);

        $this->engines->resolve('lexicon')->getCompiler()->setView($view);

        return $view;
    }
}