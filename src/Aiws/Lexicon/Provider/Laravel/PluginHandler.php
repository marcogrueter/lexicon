<?php namespace Aiws\Lexicon\Provider\Laravel;

use Aiws\Lexicon\Contract\EnvironmentInterface;
use Aiws\Lexicon\Contract\PluginHandlerInterface;
use Aiws\Lexicon\Contract\PluginInterface;

class PluginHandler implements PluginHandlerInterface
{

    /**
     * @var EnvironmentInterface
     */
    protected $lexicon;

    /**
     * @var array
     */
    protected $plugins = [];


    /**
     * Set environment
     *
     * @param EnvironmentInterface $lexicon
     * @return PluginHandlerInterface
     */
    public function setEnvironment(EnvironmentInterface $lexicon)
    {
        $this->lexicon = $lexicon;
        return $this;
    }

    /**
     * @return EnvironmentInterface
     */
    public function getEnvironment()
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
        \App::singleton("lexicon.plugin.{$name}", function() use ($class) {
                return new $class;
            });

        $this->plugins[$name] = $class;

        return $this;
    }

    /**
     * Get plugin
     *
     * @param $name
     * @return PluginInterface|null
     */
    public function get($name)
    {
        $parts = explode($this->getEnvironment()->getScopeGlue(), $name);

        if (count($parts) > 1) {
            $name = $parts[0];
            return isset($this->plugins[$name]) ? \App::make("lexicon.plugin.{$name}") : null;
        }

        return null;
    }

    /**
     * Call plugin method
     *
     * @param        $name
     * @param array  $attributes
     * @param string $content
     * @return mixed
     */
    public function call($name, $attributes = [], $content = '')
    {
        $segments = explode($this->getEnvironment()->getScopeGlue(), $name);
        if (count($segments) > 1) {
            $method = $segments[1];
            /** @var $plugin PluginInterface */
            if ($plugin = $this->get($name)) {
                $plugin->setAttributes($attributes);
                $plugin->setContent($content);
                return $plugin->{$method}();
            }
        }

        return null;
    }
}