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

    /**
     * Call filter method
     *
     * @param        $key
     * @param string $prefix
     * @internal param PluginInterface $plugin
     * @internal param $name
     * @return mixed
     */
    public function filter($key, $prefix = 'filter');

}