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
     * @param $allowPhp bool
     * @return LexiconInterface
     */
    public function setAllowPhp($allowPhp);

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
     * @return string
     */
    public function getRootContextName();

    /**
     * @return NodeBlockInterface
     */
    public function getRootNodeType();

    /**
     * Get view template
     *
     * @return string
     */
    public function getViewTemplate();

    /**
     * Get view template path
     *
     * @return string
     */
    public function getViewTemplatePath();

    /**
     * Set view template path
     *
     * @return LexiconInterface
     */
    public function setViewTemplatePath($viewTemplatePath);

    /**
     * Get view namespace
     *
     * @return string
     */
    public function getViewNamespace();

    /**
     * Get view class prefix
     *
     * @return string
     */
    public function getViewClassPrefix();

    /**
     * Set view class prefix
     *
     * @return LexiconInterface
     */
    public function setViewClassPrefix($viewClassPrefix);

    /**
     * Get view class
     *
     * @param $hash
     * @return string
     */
    public function getViewClass($hash);

    /**
     * Get full view class
     *
     * @param $hash
     * @return string
     */
    public function getFullViewClass($hash);

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

    /**
     * Get the array of parse-able paths
     *
     * @return array
     */
    public function getParsePaths();

    /**
     * @param $path
     * @return bool
     */
    public function isParsePath($path);

    /**
     * Is debug enabled
     *
     * @return bool
     */
    public function isDebug();

    /**
     * @param $degug bool
     * @return LexiconInterface
     */
    public function setDebug($degug);

}