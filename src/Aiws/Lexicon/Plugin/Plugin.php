<?php namespace Aiws\Lexicon\Plugin;

use Aiws\Lexicon\Contract\PluginInterface;

class Plugin implements PluginInterface
{
    /**
     * Attributes
     *
     * @var array
     */
    protected $attributes = array();

    /**
     * Content
     *
     * @var string
     */
    protected $content;

    /**
     * Get name
     *
     * @var string
     */
    protected $name;

    /**
     * Get plugin name
     *
     * @return string
     */
    public function getPluginName()
    {
        return $this->name;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Plugin
     */
    public function setContent($content = '')
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Set attributes
     *
     * @param array $attributes
     * @return Plugin
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * Get attribute
     *
     * @param      $name
     * @param int  $offset
     * @param null $default
     * @return mixed|null
     */
    public function getAttribute($name, $offset = 0, $default = null)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        } elseif (isset($this->attributes[$offset])) {
            $this->attributes[$offset];
            return $this->attributes[$offset];
        }

        return $default;
    }

    /**
     * Prevent errors if a method that is called does not exists
     *
     * @param $name
     * @param $arguments
     * @return null
     */
    public function __call($name, $arguments)
    {
        return null;
    }
    
}