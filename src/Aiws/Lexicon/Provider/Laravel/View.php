<?php namespace Aiws\Lexicon\Provider\Laravel;

use Illuminate\View\View as BaseView;

class View extends BaseView
{
    protected $parse = false;

    /**
     * Enable parser
     *
     * @param bool $parse
     * @return void
     */
    public function parse($parse = true)
    {
        $this->parse = $parse;
        return $this;
    }

    /**
     * Get the evaluated contents of the view.
     *
     * @return string
     */
    protected function getContents()
    {
        return $this->engine->parse($this->parse)->get($this->path, $this->gatherData());
    }


}