<?php namespace Anomaly\Lexicon;

use Anomaly\Lexicon\Conditional\ConditionalHandler;
use Anomaly\Lexicon\Contract\EnvironmentInterface;
use Anomaly\Lexicon\Contract\NodeBlockInterface;
use Anomaly\Lexicon\Contract\NodeInterface;
use Anomaly\Lexicon\Contract\PluginHandlerInterface;

class Lexicon implements EnvironmentInterface
{

    /**
     * Cache path
     *
     * @var
     */
    public $cachePath;

    /**
     * Disable cache
     *
     * @var bool
     */
    public $disableCache = true;

    /**
     * Compress source / remove excess white space
     *
     * @var bool
     */
    public $compress = false;

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
    public $maxDepth = 20;

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
    const VIEW_CLASS_PREFIX = '\\View_';

    /**
     * Data constant
     */
    const DATA = '$__data';

    /**
     * Environment (Factory) constant
     */
    const ENV = '$__data[\'__env\']';

    /**
     * @param Regex                  $regex
     * @param ConditionalHandler     $conditionalHandler
     * @param PluginHandlerInterface $pluginHandler
     */
    public function __construct(
        Regex $regex,
        ConditionalHandler $conditionalHandler = null,
        PluginHandlerInterface $pluginHandler = null
    ) {
        $this->regex              = $regex;
        $this->conditionalHandler = $conditionalHandler;
        $this->pluginHandler      = $pluginHandler->setEnvironment($this);
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
     * @return array
     */
    public function getNodeTypes()
    {
        return $this->nodeTypes;
    }

    /**
     * Get max depth
     *
     * @return int
     */
    public function getMaxDepth()
    {
        return $this->maxDepth;
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

    public function injectNoParse($text)
    {
        foreach ($this->noParseExtractions as $key => $extraction) {
            $extraction['content'] = $this->getRegex()->compress($extraction['content']);
            $text                  = str_replace($extraction['id'], $extraction['content'], $text);
            unset($this->noParseExtractions[$key]);
        }

        return $text;
    }

    /**
     * Register node type
     *
     * @param NodeInterface $nodeType
     * @return EnvironmentInterface
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
     * @return EnvironmentInterface
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
     * @return EnvironmentInterface
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
     * @return EnvironmentInterface
     */
    public function registerPlugins(array $plugins)
    {
        foreach ($plugins as $name => $plugin) {
            $this->registerPlugin($name, $plugin);
        }
        return $this;
    }


    /**
     * Get plugin
     *
     * @param $name
     * @return Contract\PluginInterface
     */
    public function getPlugin($name)
    {
        return $this->pluginHandler->get($name);
    }

    /**
     * Call plugin method
     *
     * @param        $name
     * @param array  $attributes
     * @param string $content
     * @return mixed
     */
    public function call($name, $attributes = [], $content = '')
    {
        return $this->getPluginHandler()->call($name, $attributes, $content);
    }

    /**
     * Set ignored matchers
     *
     * @param array $ignoredMatchers
     * @return EnvironmentInterface
     */
    public function setIgnoredMatchers(array $ignoredMatchers = [])
    {
        $this->ignoredMatchers = $ignoredMatchers;
        return $this;
    }

    /**
     * Get ignored matchers
     *
     * @return string
     */
    public function getIgnoredMatchers()
    {
        return implode('|', $this->ignoredMatchers);
    }

    /**
     * Get root node type
     *
     * @return NodeInterface
     */
    public function getBlockNodeType()
    {
        return $this->nodeTypes[$this->blockNodeTypeOffset];
    }

    /**
     * Get regex
     *
     * @return Regex
     */
    public function getRegex()
    {
        return $this->regex;
    }

    /**
     * Get root context name
     *
     * @return string
     */
    public function getRootContextName()
    {
        return $this->rootContextName;
    }

    /**
     * Get allow PHP
     *
     * @return bool
     */
    public function allowPhp()
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
     * Is filter
     *
     * @param $name
     * @return bool
     */
    public function isFilter($name)
    {
        return $this->getPluginHandler()->isFilter($name);
    }

    /**
     * Is filter
     *
     * @param $name
     * @return bool
     */
    public function isParse($name)
    {
        return $this->getPluginHandler()->isParse($name);
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
        return $this->getViewNamespace() . static::VIEW_CLASS_PREFIX . $hash;
    }
}