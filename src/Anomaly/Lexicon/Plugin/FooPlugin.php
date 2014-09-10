<?php namespace Anomaly\Lexicon\Plugin;

class FooPlugin extends Plugin
{

    /**
     * Plugin name
     *
     * @var string
     */
    public $name = 'foo';

    /**
     * Five
     *
     * @return int
     */
    public function five()
    {
        return 5;
    }

}