<?php namespace Aiws\Lexicon\Example;

use Aiws\Lexicon\Contract\PluginInterface;

class TestPlugin implements PluginInterface
{
    protected $attributes = array();

    protected $content;

    protected $dataTypes = [
        'hello' => 'string'
    ];

    public function getPluginName()
    {
        return 'test';
    }

    public function hello()
    {
        $name = $this->attribute('name', 'World');

        return "Hello $name!";
    }

    public function setContent($content = '')
    {
        $this->content = $content;
    }

    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function attribute($name, $default = null, $offset = 0)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        } elseif (isset($this->attributes[$offset])) {
            $this->attributes[$offset];
            return $this->attributes[$offset];
        }

        return $default;
    }
}