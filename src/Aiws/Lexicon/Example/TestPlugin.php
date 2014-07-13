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
        $name = $this->getAttribute('name', 'World');

        return "Hello {$name}!";
    }

    public function object()
    {
        $name = $this->getAttribute('name', 'yay');

        $object = new \stdClass();

        $object->property = "Plugin property {$name}!";

        return $object;
    }

    public function loop()
    {
        return [

            [
                'title' => 'Library 1',
                'books' => [
                    [
                        'title' => 'Book 1.2'
                    ],
                    [
                        'title' => 'Book 1.2'
                    ],
                ]
            ],
            [
                'title' => 'Library 2',
                'books' => [
                    [
                        'title' => 'Book 2.2'
                    ],
                    [
                        'title' => 'Book 2.2'
                    ],
                ]
            ],
        ];
    }

    public function filterMd5()
    {
        return md5($this->getContent());
    }

}