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

    public function register($class)
    {
        $segments = explode('\\', $class);

        $shortclass = $segments[count($segments)-1];

        $name = str_replace('plugin', '', strtolower($shortclass));

        $bindString = "lexicon.plugin.{$name}";

        \App::singleton($bindString, function() use ($class) {
                return new $class;
            });

        $this->plugins[$name] = $bindString;

        return $this;
    }

    public function get($name)
    {
        $segments = explode('.', $name);

        $name = $segments[0];

        return isset($this->plugins[$name]) ? \App::make("lexicon.plugin.{$name}") : null;
    }

    public function call($name, $attributes, $content)
    {
        $segments = explode('.', $name);

        if (count($segments) > 1) {
            $name   = $segments[0];
            $method = $segments[1];

            if (isset($this->pluginData[$name])) {
                return $this->pluginData[$name];
            }

            /** @var $plugin PluginInterface */
            if ($plugin = $this->lexicon->getPlugin($name)) {
                $plugin->setAttributes($attributes);
                $plugin->setContent($content);
                return $this->pluginData[$name] = $plugin->{$method}();
            }
        }

        return null;
    }
}