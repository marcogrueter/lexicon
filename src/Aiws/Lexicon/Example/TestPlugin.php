<?php namespace Aiws\Lexicon\Example;

class TestPlugin extends Plugin
{

    /**
     * Plugin name
     *
     * @var string
     */
    public $name = 'test';

    protected $restrict = ['test'];

    protected $class = 'Aiws\Lexicon\Example\Delegate';

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
}