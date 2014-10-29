<?php namespace Anomaly\Lexicon\Plugin;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\Plugin\PluginHandlerInterface;
use Anomaly\Lexicon\Contract\Plugin\PluginInterface;
use Anomaly\Lexicon\Contract\Support\ContainerInterface;
use Anomaly\Lexicon\Stub\LexiconStub;

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
     * @var string
     */
    protected $bindingPrefix = 'anomaly.lexicon.plugin.';

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
     * Get binding
     */
    public function getBinding($name)
    {
        return $this->bindingPrefix . $name;
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
        /** @var ContainerInterface $container */
        $container            = $this->getLexicon()->getContainer();
        $this->plugins[$name] = $class;
        $container->bind(
            'anomaly.lexicon.plugin' . $name,
            function () use ($class) {
                return new $class;
            },
            true
        );
        return $this;
    }

    /**
     * @param PluginInterface $plugin
     */
    public function registerPluginInstance(PluginInterface $plugin)
    {
        /** @var ContainerInterface $container */
        $container            = $this->getLexicon()->getContainer();
        $name                 = $plugin->getPluginName();
        $this->plugins[$name] = get_class($plugin);
        $container->instance($this->plugins[$name], $plugin);
    }

    /**
     * Get plugin
     *
     * @param $name
     * @return PluginInterface
     */
    public function get($name)
    {
        $lexicon   = $this->getLexicon();

        if (!strpos($name, $lexicon->getScopeGlue())) {
            return null;
        }

        $container = $lexicon->getContainer();
        $name      = explode($lexicon->getScopeGlue(), $name)[0];
        return isset($this->plugins[$name]) ? $container[$this->plugins[$name]] : null;
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
            $method  = explode($this->getLexicon()->getScopeGlue(), $name)[1];
            $isParse = $plugin->isParse($method);
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
            $method   = explode($this->getLexicon()->getScopeGlue(), $name)[1];
            $isFilter = $plugin->isFilter($method);
        }
        return $isFilter;
    }

    public static function stub()
    {
        return (new static())->setLexicon(LexiconStub::get());
    }

}