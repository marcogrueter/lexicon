<?php namespace Anomaly\Lexicon\Contract;

interface PluginHandlerInterface
{
    public function setEnvironment(LexiconInterface $lexicon);

    /**
     * Register
     *
     * @param $name
     * @param $class
     * @return PluginHandlerInterface
     */
    public function register($name, $class);

    /**
     * Get the plugin by name
     *
     * @param $name
     * @return PluginInterface
     */
    public function get($name);

    /**
     * Call the plugin method
     *
     * @param        $name
     * @param array  $attributes
     * @param string $content
     * @return mixed
     */
    public function call($name, $attributes = [], $content = '');

    /**
     * Plugin method is a filter
     *
     * @param $name
     * @return bool
     */
    public function isFilter($name);

    /**
     * Plugin method is a parse-able filter
     *
     * @param $name
     * @return bool
     */
    public function isParse($name);
}