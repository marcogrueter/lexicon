<?php namespace Anomaly\Lexicon\Plugin;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\Plugin\PluginHandlerInterface;
use Anomaly\Lexicon\Contract\Plugin\PluginInterface;

/**
 * Class PluginHandler
 *
 * @package Anomaly\Lexicon\Plugin
 */
class PluginHandler implements PluginHandlerInterface
{

    /**
     * @var LexiconInterface
     */
    protected $lexicon;

    /**
     * @var array
     */
    protected $plugins = [];


    /**
     * Set lexicon
     *
     * @param LexiconInterface $lexicon
     * @return PluginHandlerInterface
     */
    public function setLexicon(LexiconInterface $lexicon)
    {
        $this->lexicon = $lexicon;
        return $this;
    }

    /**
     * @return LexiconInterface
     */
    public function getLexicon()
    {
        return $this->lexicon;
    }

    /**
     * Register plugin
     *
     * @param $name
     * @param $class
     * @return $this
     */
    public function register($name, $class)
    {
        $this->plugins[$name] = $class;
        return $this;
    }

    /**
     * Get plugin
     *
     * @param $name
     * @return PluginInterface
     */
    public function get($name)
    {
        $parts = explode($this->getLexicon()->getScopeGlue(), $name);

        $plugin = null;

        if (count($parts) > 1) {
            $plugin = !empty($this->plugins[$parts[0]]) ? new $this->plugins[$parts[0]] : null;
        }

        return $plugin;
    }


    /**
     * Call plugin method
     *
     * @param PluginInterface $plugin
     * @param string          $method
     * @param array           $attributes
     * @param string          $content
     * @return mixed
     */
    public function call(PluginInterface $plugin, $method, $attributes = [], $content = '')
    {
        return $plugin
            ->setLexicon($this->getLexicon())
            ->setAttributes($attributes)
            ->setContent($content)
            ->{$method}();
    }

    /**
     * Is parse
     *
     * @param $name
     * @return bool
     */
    public function isParse($name)
    {
        $isParse = false;

        if ($plugin = $this->get($name)) {
            $parts   = explode($this->getLexicon()->getScopeGlue(), $name);
            $isParse = $plugin->isParse($parts[1]);
        }

        return $isParse;
    }

    /**
     * Is filter
     *
     * @param $name
     * @return bool
     */
    public function isFilter($name)
    {
        $isFilter = false;

        if ($plugin = $this->get($name)) {
            $parts    = explode($this->getLexicon()->getScopeGlue(), $name);
            $isFilter = $plugin->isFilter($parts[1]);
        }

        return $isFilter;
    }
    
}