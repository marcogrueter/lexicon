<?php namespace Anomaly\Lexicon\Example;

use Anomaly\Lexicon\Plugin\Plugin;

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