<?php namespace Anomaly\Lexicon\Plugin;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\Plugin\PluginHandlerInterface;
use Anomaly\Lexicon\Contract\Plugin\PluginInterface;

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
     * @codeCoverageIgnore
     * @param LexiconInterface $lexicon
     * @return PluginHandlerInterface
     */
    public function setLexicon(LexiconInterface $lexicon)
    {
        $this->lexicon = $lexicon;
        return $this;
    }

    /**
     * @codeCoverageIgnore
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
     * @return PluginInterface|null
     */
    public function get($name)
    {
        $parts = explode($this->getLexicon()->getScopeGlue(), $name);

        if (count($parts) > 1) {
            $name = $parts[0];
            return isset($this->plugins[$name]) ? new $this->plugins[$name] : null;
        }

        return null;
    }

    /**
     * Call plugin method
     *
     * @param        $key
     * @param array  $attributes
     * @param string $content
     * @internal param $name
     * @return mixed
     */
    public function call($key, $attributes = [], $content = '')
    {
        $segments = explode($this->getLexicon()->getScopeGlue(), $key);
        if (count($segments) > 1) {
            /** @var $plugin PluginInterface */
            if ($plugin = $this->get($key)) {

                $method = $segments[1];

                $plugin
                    ->setLexicon($this->getLexicon())
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
        $isPlugin = false;

        if ($plugin = $this->get($key)) {
            $segments = explode($this->getLexicon()->getScopeGlue(), $key);
            if (count($segments) > 1) {
                $isPlugin = method_exists($plugin, 'filter' . camel_case($segments[1]));
            }
        }

        return $isPlugin;
    }

    /**
     * Does this call have to be parsed and filtered?
     *
     * @param $key
     * @return bool
     */
    public function isParse($key)
    {
        $isParse = false;

        if ($plugin = $this->get($key) and $plugin instanceof PluginInterface) {
            $segments = explode($this->getLexicon()->getScopeGlue(), $key);
            if (count($segments) > 1) {
                $isParse = method_exists($plugin, 'parse' . studly_case($segments[1]));
            }
        }

        return $isParse;
    }

    /**
     * Run the filter from the plugin
     *
     * @param        $name
     * @param string $prefix
     * @internal param PluginInterface $plugin
     * @internal param $key
     * @return mixed
     */
    public function filter($name, $prefix = 'filter')
    {
        $result = null;

        if ($plugin = $this->get($name) and $plugin instanceof PluginInterface) {
            $segments = explode($this->getLexicon()->getScopeGlue(), $name);
            if (count($segments) > 1) {
                $result = call_user_func([$plugin, $prefix . camel_case($segments[1])]);
            }
        }

        return $result;
    }

}