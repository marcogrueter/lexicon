<?php namespace Anomaly\Lexicon\Plugin;

use Anomaly\Lexicon\Contract\EnvironmentInterface;
use Anomaly\Lexicon\Contract\PluginInterface;
use Whoops\Example\Exception;

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
     * Lexicon
     *
     * @var EnvironmentInterface
     */
    protected $lexicon;

    /**
     * Set plugin name
     *
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
     * @param int  $offset
     * @param null $default
     * @return mixed|null
     */
    public function getAttribute($name, $offset = 0, $default = null)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        } elseif (isset($this->attributes[$offset])) {
            return $this->attributes[$offset];
        }

        return $default;
    }

    /**
     * Set environment
     *
     * @param EnvironmentInterface $lexicon
     * @return $this
     */
    public function setEnvironment(EnvironmentInterface $lexicon)
    {
        $this->lexicon = $lexicon;
        return $this;
    }

    /**
     * Get environment
     *
     * @return EnvironmentInterface
     */
    public function getLexicon()
    {
        return $this->lexicon;
    }

    /**
     * Prevent errors if a method that is called does not exists
     *
     * @param $name
     * @param $arguments
     * @return null
     */
    public function __call($key, $arguments)
    {
        if (!$this->getLexicon()) {
            throw new \Exception;
        }

        $handler = $this->getLexicon()->getPluginHandler();
        $name = $this->getPluginName().'.'.$key;

        if ($handler->isFilter($name)) {
            return $handler->filter($this,$name);
        } elseif($handler->isParse($name))  {
            return $handler->filter($this,$name, 'parse');
        }

        return null;
    }

}