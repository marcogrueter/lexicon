<?php namespace Aiws\Lexicon\Example;

use Aiws\Lexicon\Plugin\Plugin;

class TestPlugin extends Plugin
{

    /**
     * Plugin name
     *
     * @var string
     */
    public $name = 'test';

    /**
     * Hello method
     *
     * @return string
     */
    public function hello()
    {
        $name = $this->attribute('name', 'World');

        return "Hello {$name}!";
    }

    public function object()
    {
        $name = $this->attribute('name', 'yay');

        $object = new \stdClass();

        $object->property = "Plugin property {$name}!";

        return $object;
    }

    public function loop()
    {
        return \Book::all();
    }
}