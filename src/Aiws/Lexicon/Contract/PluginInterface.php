<?php namespace Aiws\Lexicon\Contract;

interface PluginInterface
{

    /**
     * Set plugin name
     *
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
     * @param int  $offset
     * @param null $default
     * @return mixed
     */
    public function getAttribute($name, $offset = 0, $default = null);

}