<?php namespace Aiws\Lexicon\Adapter\Laravel;

use Aiws\Lexicon\Base\Data;
use Aiws\Lexicon\Contract\EnvironmentInterface;
use Aiws\Lexicon\Contract\PluginHandlerInterface;
use Aiws\Lexicon\Contract\PluginInterface;

class PluginHandler implements PluginHandlerInterface
{

    /**
     * @var EnvironmentInterface
     */
    protected $lexicon;

    protected $pluginData = array();

    public function setEnvironment(EnvironmentInterface $lexicon)
    {
        $this->lexicon = $lexicon;
        return $this;
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