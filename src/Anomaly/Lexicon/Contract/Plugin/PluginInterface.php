<?php namespace Anomaly\Lexicon\Contract\Plugin;

use Anomaly\Lexicon\Contract\LexiconInterface;

interface PluginInterface
{

    /**
     * Set plugin name
     *
     * @param $pluginName
     * @return PluginInterface
     */
    public function setPluginName($pluginName);

    /**
     * Get plugin name
     *
     * @return string
     */
    public function getPluginName();

    /**
     * Set content
     *
     * @param $content
     * @return PluginInterface
     */
    public function setContent($content);

    /**
     * Set attributes
     *
     * @param array $attributes
     * @return PluginInterface
     */
    public function setAttributes(array $attributes);

    /**
     * Get attribute
     *
     * @param      $name
     * @param null $default
     * @param int  $offset
     * @return mixed
     */
    public function getAttribute($name, $default = null, $offset = 0);

    /**
     * @param $key
     * @return mixed
     */
    public function isFilter($key);

    /**
     * @param $key
     * @return mixed
     */
    public function isParse($key);

    /**
     * Magic method call
     *
     * @param $key
     * @param $params
     * @return mixed
     */
    public function __call($key, array $params = []);

}