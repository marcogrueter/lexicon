<?php namespace Anomaly\Lexicon\Contract;

use Anomaly\Lexicon\Conditional\ConditionalHandler;
use Anomaly\Lexicon\Contract\Plugin\PluginHandlerInterface;
use Anomaly\Lexicon\Foundation;
use Anomaly\Lexicon\Node\NodeFactory;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;

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
     * @return string
     */
    public function getRootAlias();

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
     * Register node type
     *
     * @param        $nodeType
     * @param string $nodeGroup
     * @return LexiconInterface
     */
    public function registerNodeType($nodeType, $nodeGroup = NodeFactory::DEFAULT_NODE_GROUP);

    /**
     * @param array $nodeTypes
     * @return LexiconInterface
     */
    public function registerNodeGroup(array $nodeTypes, $nodeGroup = NodeFactory::DEFAULT_NODE_GROUP);

    /**
     * Register node sets
     *
     * @param array $nodeGroups
     * @return LexiconInterface
     */
    public function registerNodeGroups(array $nodeTypes = []);

    /**
     * Register plugin
     *
     * @param $name
     * @param $class
     * @return LexiconInterface
     */
    public function registerPlugin($name, $class);

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
    public function addStringTemplate($path);

    /**
     * Get the array of parse-able paths
     *
     * @return array
     */
    public function getStringTemplates();

    /**
     * @param $path
     * @return bool
     */
    public function isStringTemplate($path);

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

    /**
     * Set node factory
     *
     * @param NodeFactory $nodeFactory
     * @return LexiconInterface
     */
    public function setNodeFactory(NodeFactory $nodeFactory);

    /**
     * Get node factory
     *
     * @return NodeFactory
     */
    public function getNodeFactory();

    /**
     * @param Foundation $foundation
     * @return mixed
     */
    public function setFoundation(Foundation $foundation);

    /**
     * @return Foundation
     */
    public function getFoundation();

    /**
     * Get node groups
     *
     * @return array
     */
    public function getNodeGroups();

    /**
     * Get plugins
     *
     * @return array
     */
    public function getPlugins();

    /**
     * Get boolean test types
     *
     * @return array
     */
    public function getBooleanTestTypes();

    /**
     * Set config path
     *
     * @param $configPath
     * @return LexiconInterface
     */
    public function setConfigPath($configPath);

    /**
     * Get config path
     *
     * @return string
     */
    public function getConfigPath();

    /**
     * An array of string classes
     *
     * @param array $classes
     * @return LexiconInterface
     */
    public function addMagicMethodClasses(array $magicMethodClasses);

    /**
     * Add magic method class
     *
     * @param array $magicMethodClass
     * @return LexiconInterface
     */
    public function addMagicMethodClass($magicMethodClass);

    /**
     * Get magic method classes
     *
     * @return array
     */
    public function getMagicMethodClasses();

    /**
     * Is magic method object
     *
     * @param $obj
     * @return bool
     */
    public function isMagicMethodObject($obj);

    /**
     * Get compiler sequence
     *
     * @return mixed
     */
    public function getCompilerSequence();

    /**
     * Get base path
     *
     * @return mixed
     */
    public function getBasePath();

}