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
     * Call plugin method
     *
     * @param PluginInterface $plugin
     * @param string          $method
     * @param array           $attributes
     * @param string          $content
     * @return mixed
     */
    public function call(PluginInterface $plugin, $method, $attributes = [], $content = '');

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