<?php namespace Aiws\Lexicon\Adapter\Laravel;

use Aiws\Lexicon\Lexicon;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Environment as BaseEnvironment;

class Environment extends BaseEnvironment
{
    public function make($view, $data = [], $mergeData = [])
    {
        $lexiconCompiler = $this->engines->resolve('lexicon')->getCompiler();

        $view = parent::make($view, $data, $mergeData);

        $lexiconCompiler->setView($view);

        return $view;
    }

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string  $view
     * @param  array   $data
     * @param  array   $mergeData
     * @return \Illuminate\View\View
     */
    public function parse($content, $data = [], $mergeData = [])
    {
        /** @var $engine CompilerEngine */
        $engine = $this->engines->resolve('lexicon');

        $compiler = $engine->getCompiler();



        $view = md5($content);

        $data = array_merge($mergeData, $this->parseData($data));

        $this->callCreator($view = new View($this, $engine, $view, $content, $data));

        $view->parse();

        $compiler->setView($view);

        return $view;
    }

    /**
     * Append content to a given section.
     *
     * @param  string  $section
     * @param  string  $content
     * @return void
     */
    protected function extendSection($section, $content)
    {
        if (isset($this->sections[$section]))
        {
            $content = str_replace(Lexicon::PARENT_MATCHER, $content, $this->sections[$section]);

            $this->sections[$section] = $content;
        }
        else
        {
            $this->sections[$section] = $content;
        }
    }
}