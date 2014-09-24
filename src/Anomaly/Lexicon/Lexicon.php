<?php namespace Anomaly\Lexicon;

use Anomaly\Lexicon\Conditional\ConditionalHandler;
use Anomaly\Lexicon\Contract\Conditional\ConditionalHandlerInterface;
use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\Node\NodeInterface;
use Anomaly\Lexicon\Contract\Node\RootInterface;
use Anomaly\Lexicon\Contract\Plugin\PluginHandlerInterface;
use Anomaly\Lexicon\Contract\Support\Container as ContainerInterface;
use Anomaly\Lexicon\Exception\RootNodeTypeNotFoundException;
use Anomaly\Lexicon\Plugin\PluginHandler;
use Anomaly\Lexicon\Support\Container;
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
     * Ignored matchers
     *
     * @var array
     */
    public $ignoredMatchers = [];

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
     * Node types
     *
     * @var array
     */
    public $nodeTypes = [];

    /**
     * @var array
     */
    protected $attributeNodeTypes = [
        'Anomaly\Lexicon\Attribute\VariableAttribute',
        'Anomaly\Lexicon\Attribute\NamedAttribute',
        'Anomaly\Lexicon\Attribute\OrderedAttribute',
    ];

    /**
     * Plugins
     *
     * @var array
     */
    protected $plugins = [];

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
     * All node instances
     *
     * @var array
     */
    protected $nodes = [];

    /**
     * Node set paths
     *
     * @var array
     */
    protected $nodeSetPaths = [];

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
     * Data constant
     */
    const DATA = '$__data';

    /**
     * Environment (Factory) constant
     */
    const ENV = '$__data[\'__env\']';

    /**
     * Default node set
     */
    const DEFAULT_NODE_SET = 'all';

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
     * Lexicon construct
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->setConditionalHandler($this->newConditionalHandler());
        $this->setPluginHandler($this->newPluginHandler());
    }

    /**
     * Register dependencies
     *
     * @return Foundation
     */
    public function register()
    {
        return (new Foundation($this, $this->getContainer()))->register();
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container ?: $this->container = new Container();
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
     * New conditional handler
     *
     * @return ConditionalHandler
     */
    public function newConditionalHandler()
    {
        return (new ConditionalHandler());
    }

    /**
     * New plugin handler
     *
     * @return PluginHandler
     */
    public function newPluginHandler()
    {
        return (new PluginHandler())->setLexicon($this);
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
     * Get node types
     *
     * @param string $nodeSet
     * @return array
     */
    public function getNodeTypes($nodeSet = self::DEFAULT_NODE_SET)
    {
        $nodeTypes = [];

        if (isset($this->nodeTypes[$nodeSet])) {
            foreach ($this->nodeTypes[$nodeSet] as $nodeType) {
                $nodeTypes[] = $this->newNodeType($nodeType);
            }
        }

        return $nodeTypes;
    }

    /**
     * Get node types
     *
     * @param string $nodeSet
     * @return array
     */
    public function getAttributeNodeTypes()
    {
        $nodeTypes = [];

        if (isset($this->attributeNodeTypes)) {
            foreach ($this->attributeNodeTypes as $nodeType) {
                $nodeTypes[] = $this->newNodeType($nodeType);
            }
        }

        return $nodeTypes;
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
     * @param string $nodeSet
     * @return LexiconInterface
     */
    public function registerNodeType($nodeType, $nodeSet = self::DEFAULT_NODE_SET)
    {
        $this->nodeTypes[$nodeSet][$nodeType] = $nodeType;
        return $this;
    }

    /**
     * Register node types
     *
     * @param array $nodeSets
     * @return LexiconInterface
     */
    public function registerNodeSets(array $nodeSets = [])
    {
        foreach ($nodeSets as $nodeSet => $nodeTypes) {
            $this->registerNodeSet($nodeTypes, $nodeSet);
        }
        return $this;
    }

    /**
     * Register node types
     *
     * @param array $nodeTypes
     * @return LexiconInterface
     */
    public function registerNodeSet(array $nodeTypes, $nodeSet = self::DEFAULT_NODE_SET)
    {
        foreach ($nodeTypes as $nodeType) {
            $this->registerNodeType($nodeType, $nodeSet);
        }

        return $this;
    }

    /**
     * Remove node type from node set
     *
     * @param $nodeType
     * @param $nodeSet
     * @return LexiconInterface
     */
    public function removeNodeTypeFromNodeSet($nodeType, $nodeSet = self::DEFAULT_NODE_SET)
    {
        if (isset($this->nodeTypes[$nodeSet]) and isset($this->nodeTypes[$nodeSet][$nodeType])) {
            unset($this->nodeTypes[$nodeSet][$nodeType]);
        }
        return $this;
    }

    /**
     * Get node set
     *
     * @param string $nodeSet
     * @return array
     */
    public function getNodeSet($nodeSet = self::DEFAULT_NODE_SET)
    {
        return isset($this->nodeTypes[$nodeSet]) ? $this->nodeTypes[$nodeSet] : [];
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
        $this->getPluginHandler()->register($name, $class);
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
        foreach ($plugins as $name => $plugin) {
            $this->registerPlugin($name, $plugin);
        }
        return $this;
    }

    /**
     * Get root node type
     *
     * @throws RootNodeTypeNotFoundException
     * @return NodeInterface
     */
    public function getRootNodeType($nodeSet = self::DEFAULT_NODE_SET)
    {
        $block = null;

        foreach ($this->getNodeTypes($nodeSet) as $nodeType) {
            if ($nodeType instanceof RootInterface) {
                $block = $nodeType;
                break;
            }
        }

        if (!$block) {
            throw new RootNodeTypeNotFoundException;
        }

        return $block;
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
     * Add a node instance
     *
     * @param NodeInterface $node
     * @return NodeInterface
     */
    public function addNode(NodeInterface $node)
    {
        return $this->nodes[$node->getId()] = $node;
    }

    /**
     * Get node by id
     *
     * @param $id
     * @return null
     */
    public function getNodeById($id)
    {
        return isset($this->nodes[$id]) ? $this->nodes[$id] : null;
    }

    /**
     * Get instantiated nodes
     *
     * @return array
     */
    public function getNodes()
    {
        return $this->nodes;
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
     * Add node set path
     *
     * @param        $path
     * @param string $nodeSet
     * @return LexiconInterface
     */
    public function addNodeSetPath($path, $nodeSet = self::DEFAULT_NODE_SET)
    {
        $this->nodeSetPaths[$path] = $nodeSet;
        return $this;
    }

    /**
     * Get node set from path
     *
     * @param $path
     * @return string
     */
    public function getNodeSetFromPath($path)
    {
        return isset($this->nodeSetPaths[$path]) ? $this->nodeSetPaths[$path] : self::DEFAULT_NODE_SET;
    }

    /**
     * New node type
     *
     * @param $class
     * @return mixed
     */
    public function newNodeType($class)
    {
        return new $class($this);
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
        if (!$this->viewPaths) {
            $this->viewPaths = [__DIR__ . '/../../../resources/views'];
        }
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

}
