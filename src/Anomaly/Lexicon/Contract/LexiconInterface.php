<?php namespace Anomaly\Lexicon\Contract;

use Anomaly\Lexicon\Conditional\ConditionalHandler;
use Anomaly\Lexicon\Regex;

interface LexiconInterface
{
    /**
     * @return bool
     */
    public function allowPhp();

    /**
     * @return PluginHandlerInterface
     */
    public function getPluginHandler();

    /**
     * @return ConditionalHandler
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
     * Get view template
     *
     * @return string
     */
    public function getViewTemplate();

    /**
     * @param array $nodeTypes
     * @return LexiconInterface
     */
    public function registerNodeTypes(array $nodeTypes);

    /**
     * Register plugins
     *
     * @param array $plugins
     * @return LexiconInterface
     */
    public function registerPlugins(array $plugins);

    /**
     * Add parse path
     *
     * @param $path
     * @return string
     */
    public function addParsePath($path);
}