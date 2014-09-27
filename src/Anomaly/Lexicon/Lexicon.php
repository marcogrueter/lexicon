<?php namespace Anomaly\Lexicon;

use Anomaly\Lexicon\Conditional\ConditionalHandler;
use Anomaly\Lexicon\Contract\Conditional\ConditionalHandlerInterface;
use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\Plugin\PluginHandlerInterface;
use Anomaly\Lexicon\Node\NodeFactory;
use Illuminate\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Session\SessionInterface;

class Lexicon implements LexiconInterface
{
    /**
     * Scope glue
     *
     * @var string
     */
    protected $scopeGlue = '.';

    /**
     * Plugin handler
     *
     * @var PluginHandlerInterface
     */
    protected $pluginHandler;

    /**
     * Conditional
     *
     * @var ConditionalHandler
     */
    protected $conditionalHandler;

    /**
     * @var NodeFactory
     */
    protected $nodeFactory;

    /**
     * Root context name
     *
     * @var
     */
    protected $rootAlias = 'data';

    /**
     * Allow PHP
     *
     * @var bool
     */
    protected $allowPhp = false;

    /**
     * @var
     */
    protected $path;

    /**
     * @var array
     */
    protected $parsePaths = [];

    /**
     * Debug mode
     *
     * @var bool
     */
    protected $debug = false;

    /**
     * Compiled view namespace
     *
     * @var string
     */
    protected $compiledViewNamespace = 'Anomaly\Lexicon\View';


    /**
     * View class prefix
     *
     * @var string
     */
    protected $compiledViewClassPrefix = 'LexiconView_';

    /**
     * Storage path for compiled views
     *
     * @var string
     */
    protected $storagePath;

    /**
     * View paths
     *
     * @var array
     */
    protected $viewPaths;

    /**
     * @var array
     */
    protected $namespaces = [];

    /**
     * @var string
     */
    protected $extension = 'html';

    /**
     * @var Container
     */
    protected $container;

    /**
     * Plugins
     *
     * @var array
     */
    protected $plugins = [];

    /**
     * Node groups
     *
     * @var array
     */
    protected $nodeGroups = [];

    /**
     * Test types
     *
     * @var array
     */
    protected $booleanTestTypes = [];

    /**
     * Are we using the package outside of Laravel?
     *
     * @var bool
     */
    protected $standalone = false;

    /**
     * @var Foundation
     */
    protected $foundation;

    /**
     * Config path
     *
     * @var string
     */
    protected $configPath;

    /**
     * Data constant
     */
    const DATA = '$__data';

    /**
     * Environment (Factory) constant
     */
    const ENV = '$__data[\'__env\']';

    /**
     * Expected any constant
     */
    const EXPECTED_ANY = 'any';

    /**
     * Expected traversable
     */
    const EXPECTED_TRAVERSABLE = 'traversable';

    /**
     * Expected echo
     */
    const EXPECTED_ECHO = 'echo';

    /**
     * Expected numeric
     */
    const EXPECTED_NUMERIC = 'numeric';

    /**
     * Lexicon construct
     */
    public function __construct(Container $container = null)
    {
        $this->container = $container;
    }

    /**
     * Register dependencies
     *
     * @return Foundation
     */
    public function register()
    {
        $this->setFoundation((new Foundation($this))->register());
        return $this;
    }

    /**
     * Set foundation
     *
     * @param Foundation $foundation
     * @return $this
     */
    public function setFoundation(Foundation $foundation)
    {
        $this->foundation = $foundation;
        return $this;
    }

    /**
     * Get foundation
     *
     * @return Foundation
     */
    public function getFoundation()
    {
        return $this->foundation;
    }

    /**
     * @param bool $standalone
     * @return LexiconInterface
     */
    public function setStandalone($standalone = true)
    {
        $this->standalone = $standalone;
        return $this;
    }

    /**
     * Is standalone
     *
     * @return bool
     */
    public function isStandalone()
    {
        return $this->standalone;
    }

    /**
     * Set node factory
     *
     * @param NodeFactory $nodeFactory
     * @return $this
     */
    public function setNodeFactory(NodeFactory $nodeFactory)
    {
        $this->nodeFactory = $nodeFactory;
        return $this;
    }

    /**
     * Get node factory
     *
     * @return NodeFactory
     */
    public function getNodeFactory()
    {
        return $this->nodeFactory;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        if (!$this->container) {
            $this->container = new Container();

            if (method_exists($this->container, 'boot')) {
                $this->container->boot();
            }
        }

        return $this->container;
    }

    /**
     * Add namespace
     *
     * @param $namespace
     * @param $hint
     * @return $this
     */
    public function addNamespace($namespace, $hint)
    {
        $this->namespaces[$namespace] = $hint;
        return $this;
    }

    /**
     * Get namespaces
     *
     * @return array
     */
    public function getNamespaces()
    {
        return $this->namespaces;
    }

    /**
     * Set debug
     *
     * @param $debug
     * @return $this
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
        return $this;
    }

    /**
     * Is debug
     *
     * @return bool
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * Set scope glue
     *
     * @param $scopeGlue
     * @return $this
     */
    public function setScopeGlue($scopeGlue)
    {
        $this->scopeGlue = $scopeGlue;
        return $this;
    }

    /**
     * Get scope glue
     *
     * @return string
     */
    public function getScopeGlue()
    {
        return $this->scopeGlue;
    }

    /**
     * Get plugin handler
     *
     * @return PluginHandlerInterface
     */
    public function getPluginHandler()
    {
        return $this->pluginHandler;
    }

    /**
     * @return ConditionalHandler|mixed
     */
    public function getConditionalHandler()
    {
        return $this->conditionalHandler;
    }

    /**
     * Register node type
     *
     * @param        $nodeType
     * @param string $nodeGroup
     * @return LexiconInterface
     */
    public function registerNodeType($nodeType, $nodeGroup = NodeFactory::DEFAULT_NODE_GROUP)
    {
        $this->nodeGroups[$nodeGroup][] = $nodeType;
        return $this;
    }

    /**
     * Register node groups
     *
     * @param array $nodeGroups
     * @return LexiconInterface
     */
    public function registerNodeGroups(array $nodeGroups = [])
    {
        $this->nodeGroups = $nodeGroups;
        return $this;
    }

    /**
     * Register node group
     *
     * @param array $nodeTypes
     * @return LexiconInterface
     */
    public function registerNodeGroup(array $nodeTypes, $nodeGroup = NodeFactory::DEFAULT_NODE_GROUP)
    {
        $this->nodeGroups[$nodeGroup] = $nodeTypes;
        return $this;
    }

    /**
     * Get node groups
     *
     * @return array|null
     */
    public function getNodeGroups()
    {
        return $this->nodeGroups;
    }

    /**
     * Register plugin
     *
     * @param $name
     * @param $class
     * @return LexiconInterface
     */
    public function registerPlugin($name, $class)
    {
        $this->plugins[$name] = $class;
        return $this;
    }

    /**
     * Register plugins
     *
     * @param array $plugins
     * @return LexiconInterface
     */
    public function registerPlugins(array $plugins)
    {
        foreach($plugins as $name => $plugin) {
            $this->registerPlugin($name, $plugin);
        }

        return $this;
    }

    /**
     * Get plugins
     *
     * @return array
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * @return array
     */
    public function getBooleanTestTypes()
    {
        return $this->booleanTestTypes;
    }

    /**
     * Get root alias
     *
     * @return string
     */
    public function getRootAlias()
    {
        return $this->rootAlias;
    }

    /**
     * Get allow PHP
     *
     * @return bool
     */
    public function isPhpAllowed()
    {
        return $this->allowPhp;
    }

    /**
     * Set allow PHP
     *
     * @param $allowPhp
     * @return $this
     */
    public function setAllowPhp($allowPhp = false)
    {
        $this->allowPhp = $allowPhp;
        return $this;
    }

    /**
     * Add parse path
     *
     * @param $path string
     * @return $this
     */
    public function addParsePath($path)
    {
        $this->parsePaths[$path] = $path;
        return $this;
    }

    /**
     * Get parse paths
     *
     * @return array
     */
    public function getParsePaths()
    {
        return $this->parsePaths;
    }

    /**
     * Is parse path
     *
     * @param $path
     * @return bool
     */
    public function isParsePath($path)
    {
        return in_array($path, $this->parsePaths);
    }

    /**
     * Get compiled view template path
     *
     * @return string
     */
    public function getCompiledViewTemplatePath()
    {
        return __DIR__ . '/../../../resources/CompiledViewTemplate.txt';
    }

    /**
     * Get compiled view namespace
     *
     * @return string
     */
    public function getCompiledViewNamespace()
    {
        return $this->compiledViewNamespace;
    }

    /**
     * Set compiled view namespace
     *
     * @param $viewNamespace
     * @return $this
     */
    public function setCompiledViewNamespace($viewNamespace)
    {
        $this->compiledViewNamespace = $viewNamespace;
        return $this;
    }

    /**
     * Get view class prefix
     *
     * @return string
     */
    public function getCompiledViewClassPrefix()
    {
        return $this->compiledViewClassPrefix;
    }

    /**
     * Get compiled view class prefix
     *
     * @param $viewClassPrefix
     * @return LexiconInterface
     */
    public function setCompiledViewClassPrefix($viewClassPrefix)
    {
        $this->compiledViewClassPrefix = $viewClassPrefix;
        return $this;
    }

    /**
     * Get compiled view class
     *
     * @param $hash
     * @return string
     */
    public function getCompiledViewClass($hash)
    {
        return $this->getCompiledViewClassPrefix() . $hash;
    }

    /**
     * Get compiled view full class
     *
     * @param $hash
     * @return string
     */
    public function getCompiledViewFullClass($hash)
    {
        return $this->getCompiledViewNamespace() . '\\' . $this->getCompiledViewClass($hash);
    }

    /**
     * Set extension
     *
     * @param $extension
     * @return mixed
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
        return $this;
    }

    /**
     * Get the extension, defaults to html
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @return LexiconInterface
     */
    public function setViewPaths(array $paths)
    {
        $this->viewPaths = $paths;
        return $this;
    }

    /**
     * Get view paths
     *
     * @return array
     */
    public function getViewPaths()
    {
        return $this->viewPaths;
    }

    /**
     * Set storage path
     *
     * @param $path
     * @return LexiconInterface
     */
    public function setStoragePath($storagePath)
    {
        $this->storagePath = $storagePath;
        return $this;
    }

    /**
     * Get storage path
     *
     * @return string
     */
    public function getStoragePath()
    {
        return $this->storagePath;
    }

    /**
     * Set the conditional handler
     *
     * @param ConditionalHandlerInterface $conditionalHandler
     * @return $this
     */
    public function setConditionalHandler(ConditionalHandlerInterface $conditionalHandler)
    {
        $this->conditionalHandler = $conditionalHandler;
        return $this;
    }

    /**
     * Set the plugin handler
     *
     * @param PluginHandlerInterface $pluginHandler
     * @return $this
     */
    public function setPluginHandler(PluginHandlerInterface $pluginHandler)
    {
        $this->pluginHandler = $pluginHandler;
        return $this;
    }

    /**
     * Set config path
     *
     * @param $configPath
     * @return LexiconInterface
     */
    public function setConfigPath($configPath)
    {
        $this->configPath = $configPath;
        return $this;
    }

    /**
     * Get config path
     *
     * @return string
     */
    public function getConfigPath()
    {
        return $this->configPath;
    }
}
