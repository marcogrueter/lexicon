<?php namespace Anomaly\Lexicon\Plugin;

use Anomaly\Lexicon\Contract\Plugin\PluginInterface;

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
     * Set plugin name
     *
     * @param $name
     * @return string
     */
    public function setPluginName($name)
    {
        $this->name = $name;
        return $this;
    }

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
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
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
     * @param null $default
     * @param int  $offset
     * @return mixed|null
     */
    public function getAttribute($name, $default = null, $offset = 0)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        } elseif (isset($this->attributes[$offset])) {
            return $this->attributes[$offset];
        }

        return $default;
    }

    /**
     * Prevent errors if a method that is called does not exists
     *
     * @param $key
     * @param $arguments
     * @internal param $name
     * @return null
     */
    public function __call($key, array $params = [])
    {
        $result = null;

        if ($this->isFilter($key)) {
            $result = $this->{camel_case('filter_' . $key)}();
        } elseif ($this->isParse($key)) {
            $result = $this->{camel_case('parse_' . $key)}();
        }

        return $result;
    }

    /**
     * @param $key
     * @return bool
     */
    public function isFilter($key)
    {
        return method_exists($this, camel_case('filter_' . $key));
    }

    /**
     * @param $key
     * @return bool
     */
    public function isParse($key)
    {
        return method_exists($this, camel_case('parse_' . $key));
    }

}