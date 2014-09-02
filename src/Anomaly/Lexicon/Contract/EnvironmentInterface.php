<?php namespace Anomaly\Lexicon\Contract;

use Anomaly\Lexicon\Regex;
use Anomaly\Lexicon\Expected;

interface EnvironmentInterface
{
    /**
     * @return PluginHandlerInterface
     */
    public function getPluginHandler();

    /**
     * @return mixed
     */
    public function getConditionalHandler();

    /**
     * @return string
     */
    public function getScopeGlue();

    /**
     * @return int
     */
    public function getMaxDepth();

    /**
     * @return array
     */
    public function getNodeTypes();

    /**
     * @return array
     */
    public function getIgnoredMatchers();

    /**
     * @return string
     */
    public function getLexiconVariable();

    /**
     * @return Regex
     */
    public function getRegex();

    /**
     * @param        $name
     * @return PluginInterface
     */
    public function getPlugin($name);

    /**
     * @return string
     */
    public function getRootContextName();

    /**
     * @return NodeBlockInterface
     */
    public function getBlockNodeType();

    /**
     * @param        $name
     * @param array  $attributes
     * @param string $content
     * @return mixed
     */
    public function call($name, $attributes = [], $content = '');

    /**
     * @param        $data
     * @param        $key
     * @param array  $attributes
     * @param string $content
     * @return mixed
     */
    public function get($data, $key, array $attributes = [], $content = '', $default = null, $expected = Expected::ANY);

    /**
     * Get view template
     *
     * @return string
     */
    public function getViewTemplate();
}