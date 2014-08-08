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
            return isset($this->plugins[$name]) ? new $this->plugins[$name] : null;
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
    public function call($key, $attributes = [], $content = '')
    {
        $segments = explode($this->getEnvironment()->getScopeGlue(), $key);
        if (count($segments) > 1) {
            /** @var $plugin PluginInterface */
            if ($plugin = $this->get($key)) {

                $name   = $segments[0];
                $method = $segments[1];

                $plugin
                    ->setEnvironment($this->getEnvironment())
                    //->setPluginName($name)
                    ->setAttributes($attributes)
                    ->setContent($content);
                return $plugin->{$method}();
            }
        }

        return null;
    }

    /**
     * Does this call have to be filtered?
     *
     * @param $key
     * @return bool
     */
    public function isFilter($key)
    {
        if ($plugin = $this->get($key)) {
            $segments = explode('.', $key);
            if (count($segments) > 1) {
                return method_exists($plugin, 'filter' . ucfirst($segments[1]));
            }
        }

        return false;
    }

    /**
     * Does this call have to be parsed and filtered?
     *
     * @param $key
     * @return bool
     */
    public function isParse($key)
    {
        if ($plugin = $this->get($key)) {
            $segments = explode('.', $key);
            if (count($segments) > 1) {
                return method_exists($plugin, 'parse' . ucfirst($segments[1]));
            }
        }

        return false;
    }

    /**
     * Run the filter from the plugin
     *
     * @param PluginInterface $plugin
     * @param                 $key
     * @return mixed
     */
    public function filter($plugin, $key, $prefix = 'filter')
    {
        $segments = explode('.', $key);
        if (count($segments) > 1) {
            return call_user_func([$plugin, $prefix . ucfirst($segments[1])]);
        }

        return null;
    }

}