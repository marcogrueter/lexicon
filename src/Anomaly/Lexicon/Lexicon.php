<?php namespace Anomaly\Lexicon;

use Anomaly\Lexicon\Conditional\ConditionalHandler;
use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\NodeBlockInterface;
use Anomaly\Lexicon\Contract\NodeInterface;
use Anomaly\Lexicon\Contract\PluginHandlerInterface;

class Lexicon implements LexiconInterface
{
    /**
     * Scope glue
     *
     * @var string
     */
    public $scopeGlue = '.';

    /**
     * Max depth
     *
     * @var int
     */
    public $maxDepth = 100;

    /**
     * Ignored matchers
     *
     * @var array
     */
    public $ignoredMatchers = [];

    /**
     * No parse extractions
     *
     * @var array
     */
    protected $noParseExtractions = array();

    /**
     * Plugin handler
     *
     * @var Contract\PluginHandlerInterface
     */
    protected $pluginHandler;

    /**
     * Conditional
     *
     * @var ConditionalHandler
     */
    protected $conditionalHandler;

    /**
     * Runtime view cache
     *
     * @var
     */
    protected $cache = [];

    /**
     * Block node type offset
     *
     * @var int
     */
    public $blockNodeTypeOffset;

    /**
     * Node types
     *
     * @var array
     */
    public $nodeTypes = array();

    /**
     * Plugins
     *
     * @var array
     */
    protected $plugins = array();

    /**
     * Root context name
     *
     * @var
     */
    protected $rootContextName = 'data';

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
     * @var bool
     */
    protected $development = false;

    /**
     * @var array
     */
    protected $parsePaths = [];

    /**
     * Debug mode
     *
     * @var bool
     */
    protected $debug = true;

    /**
     * View template
     *
     * @var string
     */
    protected $viewTemplate;

    /**
     * @var string
     */
    protected $viewNamespace = 'Anomaly\Lexicon\View';

    /**
     * View class prefix constant
     */
    const VIEW_PREFIX = '\\View_';

    /**
     * Data constant
     */
    const DATA = '$__data';

    /**
     * Environment (Factory) constant
     */
    const ENV = '$__data[\'__env\']';

    /**
     * Any expected constant
     */
    const ANY = 'any';

    /**
     * Traversable
     */
    const TRAVERSABLE = 'traversable';

    /**
     * Echo able
     */
    const ECHOABLE = 'echoable';

    /**
     * @param ConditionalHandler     $conditionalHandler
     * @param PluginHandlerInterface $pluginHandler
     */
    public function __construct(
        ConditionalHandler $conditionalHandler = null,
        PluginHandlerInterface $pluginHandler = null
    ) {
        $this->conditionalHandler = $conditionalHandler;
        $this->pluginHandler      = $pluginHandler->setEnvironment($this);
    }

    /**
     * Set debug
     *
     * @codeCoverageIgnore
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
     * @codeCoverageIgnore
     * @return bool
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * Set scope glue
     *
     * @codeCoverageIgnore
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
     * @codeCoverageIgnore
     * @return string
     */
    public function getScopeGlue()
    {
        return $this->scopeGlue;
    }

    /**
     * Get node types
     *
     * @codeCoverageIgnore
     * @return array
     */
    public function getNodeTypes()
    {
        return $this->nodeTypes;
    }

    /**
     * Get max depth
     *
     * @codeCoverageIgnore
     * @return int
     */
    public function getMaxDepth()
    {
        return $this->maxDepth;
    }

    /**
     * Get plugin handler
     *
     * @codeCoverageIgnore
     * @return PluginHandlerInterface
     */
    public function getPluginHandler()
    {
        return $this->pluginHandler;
    }

    /**
     * @codeCoverageIgnore
     * @return ConditionalHandler|mixed
     */
    public function getConditionalHandler()
    {
        return $this->conditionalHandler;
    }

    /**
     * Register node type
     *
     * @param NodeInterface $nodeType
     * @return LexiconInterface
     */
    public function registerNodeType($nodeType)
    {
        $nodeType = new $nodeType($this);
        if ($nodeType instanceof NodeInterface) {
            $this->nodeTypes[] = $nodeType;
            if ($nodeType instanceof NodeBlockInterface) {
                end($this->nodeTypes);
                $this->blockNodeTypeOffset = key($this->nodeTypes);
            }
        }
        return $this;
    }

    /**
     * Register node types
     *
     * @param array $nodeTypes
     * @return LexiconInterface
     */
    public function registerNodeTypes(array $nodeTypes)
    {
        foreach ($nodeTypes as $nodeType) {
            $this->registerNodeType($nodeType);
        }
        return $this;
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
        $this->pluginHandler->register($name, $class);
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
     * Set ignored matchers
     *
     * @codeCoverageIgnore
     * @param array $ignoredMatchers
     * @return LexiconInterface
     */
    public function setIgnoredMatchers(array $ignoredMatchers = [])
    {
        $this->ignoredMatchers = $ignoredMatchers;
        return $this;
    }


    /**
     * Get root node type
     *
     * @return NodeInterface
     */
    public function getBlockNodeType()
    {
        return isset($this->nodeTypes[$this->blockNodeTypeOffset]) ? $this->nodeTypes[$this->blockNodeTypeOffset] : null;
    }

    /**
     * Get root context name
     *
     * @codeCoverageIgnore
     * @return string
     */
    public function getRootContextName()
    {
        return $this->rootContextName;
    }

    /**
     * Get allow PHP
     *
     * @codeCoverageIgnore
     * @return bool
     */
    public function allowPhp()
    {
        return $this->allowPhp;
    }

    /**
     * Set allow PHP
     *
     * @codeCoverageIgnore
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
     * @codeCoverageIgnore
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
     * Get view template
     *
     * @return string
     */
    public function getViewTemplate()
    {
        if ($this->viewTemplate) {
            return $this->viewTemplate;
        }

        return $this->viewTemplate = file_get_contents(__DIR__ . '/../../../assets/view.txt');
    }

    /**
     * Get view namespace
     *
     * @codeCoverageIgnore
     * @return string
     */
    public function getViewNamespace()
    {
        return $this->viewNamespace;
    }

    /**
     * Get view class
     *
     * @param $hash
     * @return string
     */
    public function getViewClass($hash)
    {
        return $this->getViewNamespace() . static::VIEW_PREFIX . $hash;
    }
}