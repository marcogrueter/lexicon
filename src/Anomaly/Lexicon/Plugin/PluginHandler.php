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
     * @return PluginHandler
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
     * Register plugins
     *
     * @param array $plugins
     * @return PluginHandler
     */
    public function registerPlugins(array $plugins)
    {
        foreach ($plugins as $name => $class) {
            $this->registerPlugin($name, $class);
        }
        return $this;
    }

    /**
     * Register plugin
     *
     * @param string $name
     * @param string $class
     * @return PluginHandler
     */
    public function registerPlugin($name, $class)
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
    public function call(PluginInterface $plugin, $method, array $attributes = [], $content = '')
    {
        $plugin->setAttributes($attributes);
        $plugin->setContent($content);
        return $plugin->{$method}();
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