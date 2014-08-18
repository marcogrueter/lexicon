<?php namespace Aiws\Lexicon\Provider\Laravel;

use Aiws\Lexicon\Util\Regex;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Factory;

class Environment extends Factory
{
    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string  $view
     * @param  array   $data
     * @param  array   $mergeData
     * @return \Illuminate\View\View
     */
    public function parse($view, $data = [], $mergeData = [])
    {
        $this->container['lexicon']->addParsePath($view);

        /** @var $engine CompilerEngine */
        $engine = $this->container['lexicon.compiler.engine'];

        $data = array_merge($mergeData, $this->parseData($data));

        $this->callCreator($view = new View($this, $engine, md5($view), $view, $data));

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
