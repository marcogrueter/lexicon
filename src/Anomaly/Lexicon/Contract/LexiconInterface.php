<?php namespace Anomaly\Lexicon\Contract;

use Anomaly\Lexicon\Conditional\ConditionalHandler;
use Anomaly\Lexicon\Contract\Node\BlockInterface;
use Anomaly\Lexicon\Contract\Node\NodeInterface;
use Anomaly\Lexicon\Contract\Plugin\PluginHandlerInterface;
use Anomaly\Lexicon\Lexicon;

interface LexiconInterface
{
    /**
     * Is PHP allowed?
     *
     * @return bool
     */
    public function isPhpAllowed();

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
     * @return array
     */
    public function getNodeTypes();

    /**
     * @return string
     */
    public function getRootContextName();

    /**
     * @return BlockInterface
     */
    public function getRootNodeType();

    /**
     * Get view template path
     *
     * @return string
     */
    public function getViewTemplatePath();

    /**
     * Set view template path
     *
     * @param $viewTemplatePath
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
     * @param $viewClassPrefix
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
    public function registerNodeSet(array $nodeTypes, $nodeSet = Lexicon::DEFAULT_NODE_SET);

    /**
     * @param string $nodeSet
     * @return array
     */
    public function getNodeSet($nodeSet = Lexicon::DEFAULT_NODE_SET);

    /**
     * Register node sets
     *
     * @param array $nodeSets
     * @return LexiconInterface
     */
    public function registerNodeSets(array $nodeTypes = []);

    /**
     * Remove node type from node set
     *
     * @param $nodeType
     * @param $nodeSet
     * @return LexiconInterface
     */
    public function removeNodeTypeFromNodeSet($nodeType, $nodeSet);

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
     * @param $debug bool
     * @return LexiconInterface
     */
    public function setDebug($debug);

    /**
     * Get node by id
     *
     * @param $id
     * @return NodeInterface|null
     */
    public function getNodeById($id);

    /**
     * Get nodes
     *
     * @return array
     */
    public function getNodes();

    /**
     * Add a node instance
     *
     * @param NodeInterface $node
     * @return LexiconInterface
     */
    public function addNode(NodeInterface $node);

    /**
     * Add node set path
     *
     * @param $path
     * @return LexiconInterface
     */
    public function addNodeSetPath($path);

    /**
     * Get node set from path
     *
     * @param $path
     * @return string
     */
    public function getNodeSetFromPath($path);
}