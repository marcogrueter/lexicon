<?php namespace Anomaly\Lexicon\Contract\Plugin;

use Anomaly\Lexicon\Contract\LexiconInterface;

interface PluginHandlerInterface
{
    /**
     * Set Lexicon
     *
     * @param LexiconInterface $lexicon
     * @return mixed
     */
    public function setLexicon(LexiconInterface $lexicon);

    /**
     * Register plugins
     *
     * @param array $plugins
     * @return PluginHandlerInterface
     */
    public function registerPlugins(array $plugins);

    /**
     * Register plugin
     *
     * @param string $name
     * @param string $class
     * @return PluginHandlerInterface
     */
    public function registerPlugin($name, $class);

    /**
     * Get the plugin by name
     *
     * @param $name
     * @return PluginInterface
     */
    public function get($name);

    /**
     * Call plugin method
     *
     * @param PluginInterface $plugin
     * @param string          $method
     * @param array           $attributes
     * @param string          $content
     * @return mixed
     */
    public function call(PluginInterface $plugin, $method, array $attributes = [], $content = '');

    /**
     * Is parse
     *
     * @param $name
     * @return bool
     */
    public function isParse($name);

    /**
     * Is filter
     *
     * @param $name
     * @return bool
     */
    public function isFilter($name);

}