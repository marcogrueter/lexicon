<?php namespace Anomaly\Lexicon\Contract;

use Anomaly\Lexicon\Conditional\ConditionalHandler;
use Anomaly\Lexicon\Contract\Node\BlockInterface;
use Anomaly\Lexicon\Contract\Node\NodeInterface;
use Anomaly\Lexicon\Contract\Plugin\PluginHandlerInterface;
use Anomaly\Lexicon\Contract\Support\Container;
use Anomaly\Lexicon\Foundation;
use Anomaly\Lexicon\Lexicon;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Session\SessionInterface;

interface LexiconInterface
{

    /**
     * @return Foundation
     */
    public function register();

    /**
     * @return Container
     */
    public function getContainer();

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
    public function getRootAlias();

    /**
     * @return BlockInterface
     */
    public function getRootNodeType();

    /**
     * Get compiled view template path
     *
     * @return string
     */
    public function getCompiledViewTemplatePath();

    /**
     * Get compiled view namespace
     *
     * @return string
     */
    public function getCompiledViewNamespace();

    /**
     * Get compiled view class prefix
     *
     * @return string
     */
    public function getCompiledViewClassPrefix();

    /**
     * Set view class prefix
     *
     * @param $viewClassPrefix
     * @return LexiconInterface
     */
    public function setCompiledViewClassPrefix($viewClassPrefix);

    /**
     * Get compiled view class
     *
     * @param $hash
     * @return string
     */
    public function getCompiledViewClass($hash);

    /**
     * Get compiled view full class
     *
     * @param $hash
     * @return string
     */
    public function getCompiledViewFullClass($hash);

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
     * @return LexiconInterface
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

    /**
     * Set extension
     *
     * @param $extension
     * @return mixed
     */
    public function setExtension($extension);

    /**
     * Get the extension, defaults to html
     *
     * @return string
     */
    public function getExtension();

    /**
     * @return LexiconInterface
     */
    public function setViewPaths(array $paths);

    /**
     * Get view paths
     *
     * @return array
     */
    public function getViewPaths();

    /**
     * Set storage path
     *
     * @param $path
     * @return LexiconInterface
     */
    public function setStoragePath($path);

    /**
     * Get storage path
     *
     * @return string
     */
    public function getStoragePath();

    /**
     * Add namespace
     *
     * @param $namespace
     * @param $hint
     * @return $this
     */
    public function addNamespace($namespace, $hint);

    /**
     * Get namespaces
     *
     * @return array
     */
    public function getNamespaces();

}