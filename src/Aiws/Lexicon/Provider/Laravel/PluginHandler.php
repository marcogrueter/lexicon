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

    protected $pluginData = array();

    public function setEnvironment(EnvironmentInterface $lexicon)
    {
        $this->lexicon = $lexicon;
        return $this;
    }

    public function register($name, $class)
    {
        \App::singleton("lexicon.plugin.{$name}", function() use ($class) {
                return new $class;
            });

        $this->plugins[$name] = $class;

        return $this;
    }

    public function get($name)
    {
        $segments = explode('.', $name);

        $name = $segments[0];

        return isset($this->plugins[$name]) ? \App::make("lexicon.plugin.{$name}") : null;
    }

    public function call($name, $attributes = [], $content = '')
    {
        $tagname = $name;

        $segments = explode('.', $name);

        if (count($segments) > 1) {
            $name   = $segments[0];
            $method = $segments[1];

            if (isset($this->pluginData[$tagname])) {
                return $this->pluginData[$tagname];
            }

            /** @var $plugin PluginInterface */
            if ($plugin = $this->get($name)) {
                $plugin->setAttributes($attributes);
                $plugin->setContent($content);
                return $this->pluginData[$tagname] = $plugin->{$method}();
            }
        }

        return null;
    }
}