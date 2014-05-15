<?php namespace Aiws\Lexicon\Adapter\Laravel;

use Aiws\Lexicon\Lexicon;
use Illuminate\View\Environment as BaseEnvironment;

class Environment extends BaseEnvironment
{
    public function make($view, $data = array(), $mergeData = array())
    {
        $view = parent::make($view, $data, $mergeData);

        $this->engines->resolve('lexicon')->getCompiler()->setView($view);

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
        $lexicon = $this->engines->resolve('lexicon')->getCompiler();

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