<?php namespace Aiws\Lexicon\Plugin;

use Aiws\Lexicon\Contract\PluginInterface;

class Plugin implements PluginInterface
{
    protected $attributes = array();

    protected $content;

    public $name;

    public function getPluginName()
    {
        return $this->name;
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

    public function __call($name, $arguments)
    {
        return null;
    }
    
}