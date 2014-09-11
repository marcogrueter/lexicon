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
    protected $scopeGlue = '.';

    /**
     * Max depth
     *
     * @var int
     */
    protected $maxDepth = 100;

    /**
     * Ignored matchers
     *
     * @var array
     */
    public $ignoredMatchers = [];

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
     * Node types
     *
     * @var array
     */
    public $nodeTypes = [];

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
     * View template path
     *
     * @var string
     */
    protected $viewTemplatePath;

    /**
     * @var string
     */
    protected $viewNamespace = 'Anomaly\Lexicon\View';

    /**
     * View class prefix
     */
    protected $viewClassPrefix = 'LexiconView_';

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
        $this->pluginHandler      = $pluginHandler->setLexicon($this);
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
     * @return array
     */
    public function getNodeTypes()
    {
        $nodeTypes = [];

        foreach($this->nodeTypes as $nodeType) {
            $nodeTypes[] = new $nodeType($this);
        }

        return $nodeTypes;
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
     * @param $nodeType
     * @return LexiconInterface
     */
    public function registerNodeType($nodeType)
    {
        $this->nodeTypes[$nodeType] = $nodeType;
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
        $this->nodeTypes = $nodeTypes;
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
    public function getRootNodeType()
    {
        $block = null;

        foreach($this->getNodeTypes() as $nodeType) {
            if ($nodeType instanceof NodeBlockInterface and $nodeType->isRoot()) {
                $block = $nodeType;
                break;
            }
        }

        if (!$block) {
            // @todo - throw exception
        }

        return $block;
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
     * Get view template path
     *
     * @return string
     */
    public function getViewTemplatePath()
    {
        return $this->viewTemplatePath;
    }

    /**
     * Get view template
     *
     * @return string
     */
    public function getViewTemplate()
    {
        return file_get_contents($this->getViewTemplatePath());
    }

    /**
     * Set view template path
     *
     * @return LexiconInterface
     */
    public function setViewTemplatePath($viewTemplatePath)
    {
        $this->viewTemplatePath = $viewTemplatePath;
        return $this;
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
     * Set view namespace
     *
     * @param $viewNamespace
     * @return $this
     */
    public function setViewNamespace($viewNamespace)
    {
        $this->viewNamespace = $viewNamespace;
        return $this;
    }

    /**
     * Get view class prefix
     *
     * @return string
     */
    public function getViewClassPrefix()
    {
        return $this->viewClassPrefix;
    }

    /**
     * Get view class prefix
     *
     * @param $viewClassPrefix
     * @return LexiconInterface
     */
    public function setViewClassPrefix($viewClassPrefix)
    {
        $this->viewClassPrefix = $viewClassPrefix;
        return $this;
    }

    /**
     * Get view class
     *
     * @param $hash
     * @return string
     */
    public function getViewClass($hash)
    {
        return $this->getViewClassPrefix() . $hash;
    }

    /**
     * Get full view class
     *
     * @param $hash
     * @return string
     */
    public function getFullViewClass($hash)
    {
        return $this->getViewNamespace() . '\\' . $this->getViewClass($hash);
    }
}