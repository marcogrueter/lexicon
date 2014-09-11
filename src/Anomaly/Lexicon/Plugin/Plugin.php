<?php namespace Anomaly\Lexicon\Plugin;

use Anomaly\Lexicon\Contract\LexiconInterface;
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
     * Lexicon
     *
     * @var LexiconInterface
     */
    protected $lexicon;

    /**
     * Set plugin name
     *
     * @codeCoverageIgnore
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
     * @codeCoverageIgnore
     * @return string
     */
    public function getPluginName()
    {
        return $this->name;
    }

    /**
     * Set content
     *
     * @codeCoverageIgnore
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
     * @codeCoverageIgnore
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set attributes
     *
     * @codeCoverageIgnore
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
     * @codeCoverageIgnore
     * @param LexiconInterface $lexicon
     * @return $this
     */
    public function setLexicon(LexiconInterface $lexicon)
    {
        $this->lexicon = $lexicon;
        return $this;
    }

    /**
     * Get environment
     *
     * @codeCoverageIgnore
     * @return LexiconInterface
     */
    public function getLexicon()
    {
        return $this->lexicon;
    }

    /**
     * Prevent errors if a method that is called does not exists
     *
     * @param $key
     * @param $arguments
     * @internal param $name
     * @return null
     */
    public function __call($key, $arguments)
    {
        $handler = $this->getLexicon()->getPluginHandler();
        $name    = $this->getPluginName() . '.' . $key;

        if ($handler->isFilter($name)) {
            return $handler->filter($this, $name);
        } elseif ($handler->isParse($name)) {
            return $handler->filter($this, $name, 'parse');
        }

        return null;
    }

}