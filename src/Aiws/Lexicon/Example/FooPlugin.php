<?php namespace Aiws\Lexicon\Example;

use Aiws\Lexicon\Plugin\Plugin;

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