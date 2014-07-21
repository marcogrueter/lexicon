<?php namespace Aiws\Lexicon\Provider\Laravel;

use Aiws\Lexicon\Util\Regex;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Environment as BaseEnvironment;

class Environment extends BaseEnvironment
{
    public function make($view, $data = [], $mergeData = [])
    {
        $path = $this->finder->find($view);

        $data = array_merge($mergeData, $this->parseData($data));

        $this->callCreator($view = new View($this, $this->getEngineFromPath($path)->parse(false), $view, $path, $data));

        $lexiconCompiler = $this->engines->resolve('lexicon')->getCompiler();

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
        $engine = $this->engines->resolve('lexicon');

        $lexicon = $engine->getCompiler()->getEnvironment();

        $regex = new Regex($lexicon);

        $content = $regex->compress($content);

        if (isset($this->sections[$section]))
        {
            $content = str_replace('{{ parent }}', $content, $this->sections[$section]);

            $this->sections[$section] = $content;
        }
        else
        {
            $this->sections[$section] = $content;
        }
    }

}
