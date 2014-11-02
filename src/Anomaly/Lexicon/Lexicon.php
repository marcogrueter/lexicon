<?php namespace Anomaly\Lexicon;

use Anomaly\Lexicon\Conditional\ConditionalHandler;
use Anomaly\Lexicon\Contract\Conditional\ConditionalHandlerInterface;
use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\Plugin\PluginHandlerInterface;
use Anomaly\Lexicon\Node\NodeFactory;
use Illuminate\Container\Container;

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
    protected $stringTemplates = [];

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
     * @var
     */
    protected $compilerSequence = [];

    /**
     * Magic method objects
     *
     * @var array
     */
    protected $magicMethodClasses = [
        'Illuminate\Database\Eloquent\Relations\Relation'
    ];

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
     * Base path
     *
     * @var string
     */
    protected $basePath;

    /**
     * Data constant
     */
    const DATA = '$__data';

    /**
     * Factory constant
     */
    const FACTORY = '$__data[\'__env\']';

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
    const EXPECTED_STRING = 'string';

    /**
     * Expected numeric
     */
    const EXPECTED_NUMERIC = 'numeric';

    /**
     * Lexicon construct
     *
     * @param Container $container
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
     * @return Lexicon
     */
    public function registerNodeType($nodeType, $nodeGroup = NodeFactory::DEFAULT_NODE_GROUP)
    {
        $this->nodeGroups[$nodeGroup][$nodeType] = $nodeType;
        return $this;
    }

    /**
     * Register node groups
     *
     * @param array $nodeGroups
     * @return Lexicon
     */
    public function registerNodeGroups(array $nodeGroups = [])
    {
        foreach ($nodeGroups as $nodeGroup => $nodeTypes) {
            $this->registerNodeGroup($nodeTypes, $nodeGroup);
        }
        return $this;
    }

    /**
     * Register node group
     *
     * @param array $nodeTypes
     * @return Lexicon
     */
    public function registerNodeGroup(array $nodeTypes, $nodeGroup = NodeFactory::DEFAULT_NODE_GROUP)
    {
        foreach ($nodeTypes as $nodeType) {
            $this->registerNodeType($nodeType, $nodeGroup);
        }
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
     * @return Lexicon
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
     * @return Lexicon
     */
    public function registerPlugins(array $plugins)
    {
        foreach ($plugins as $name => $plugin) {
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
     * Add string template
     *
     * @param $path string
     * @return $this
     */
    public function addStringTemplate($path)
    {
        $this->stringTemplates[$path] = $path;
        return $this;
    }

    /**
     * Get string templates
     *
     * @return array
     */
    public function getStringTemplates()
    {
        return $this->stringTemplates;
    }

    /**
     * Is string template
     *
     * @param $path
     * @return bool
     */
    public function isStringTemplate($string)
    {
        return in_array($string, $this->getStringTemplates());
    }

    /**
     * Get compiled view template path
     *
     * @return string
     */
    public function getCompiledViewTemplatePath()
    {
        return __DIR__ . '/CompiledViewTemplate.txt';
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
     * @return Lexicon
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
     * @return Lexicon
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
     * @return Lexicon
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
     * @return Lexicon
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

    /**
     * Register boolean test type
     *
     * @param $type
     * @param $class
     * @return Lexicon
     */
    public function registerBooleanTestType($type, $class)
    {
        $this->booleanTestTypes[$type] = $class;
        return $this;
    }

    /**
     * Register boolean test types
     *
     * @param array $booleanTestTypes
     * @return Lexicon
     */
    public function registerBooleanTestTypes(array $booleanTestTypes)
    {
        foreach ($booleanTestTypes as $type => $class) {
            $this->registerBooleanTestType($type, $class);
        }
        return $this;
    }

    /**
     * Add magic method classes
     *
     * @param $magicMethodClasses
     * @return $this
     */
    public function addMagicMethodClasses(array $magicMethodClasses)
    {
        foreach ($magicMethodClasses as $magicMethodClass) {
            $this->addMagicMethodClass($magicMethodClass);
        }
        return $this;
    }

    /**
     * Add magic method class
     *
     * @param $magicMethodClass
     * @return $this
     */
    public function addMagicMethodClass($magicMethodClass)
    {
        $this->magicMethodClasses[] = $magicMethodClass;
        return $this;
    }

    /**
     * Get magic method classes
     *
     * @return array
     */
    public function getMagicMethodClasses()
    {
        return $this->magicMethodClasses;
    }

    /**
     * Is magic method object
     *
     * @param $obj
     * @return bool
     */
    public function isMagicMethodObject($obj)
    {
        $classes = [];

        if (is_object($obj)) {
            $class   = get_class($obj);
            $parents = class_parents($obj);
            $classes = array_intersect(
                array_values(array_merge($parents, [$class])),
                $this->getMagicMethodClasses()
            );
        }

        return !empty($classes);
    }

    /**
     * Get compiler sequence
     *
     * @return mixed
     */
    public function getCompilerSequence()
    {
        return $this->compilerSequence;
    }

    /**
     * Set base path
     *
     * @param $basePath
     * @return $this
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
        return $this;
    }

    /**
     * Get base path
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath ?: __DIR__ . '/../../..';
    }
}
