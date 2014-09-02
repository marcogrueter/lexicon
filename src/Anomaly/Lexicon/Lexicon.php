<?php namespace Anomaly\Lexicon;

use Anomaly\Lexicon\Conditional\ConditionalHandler;
use Anomaly\Lexicon\Contract\EnvironmentInterface;
use Anomaly\Lexicon\Contract\NodeBlockInterface;
use Anomaly\Lexicon\Contract\NodeInterface;
use Anomaly\Lexicon\Contract\PluginHandlerInterface;
use Anomaly\Lexicon\View\Compiler\StreamCompiler;
use Anomaly\Lexicon\View\Compiler\ViewCompiler;

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
     * Environment variable source
     *
     * @var string
     */
    protected $environmentVariable = '$this->data';

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
     * Is optimized - optimizes view rendering performance, by loading views only once, even within a foreach loop
     *
     * @var bool
     */
    protected $isOptimized = false;


    /**
     * Optimized class prefix. The hash for the view is appended.
     *
     * @var string
     */
    protected $optimizeViewClass = 'AnomalyLexiconView__';

    /**
     * @var bool
     */
    protected $development = false;

    /**
     * @var array
     */
    protected $parsePaths = [];

    /**
     * View template
     *
     * @var string
     */
    protected $viewTemplate;

    protected $viewNamespace = 'Anomaly\Lexicon\View';

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
     * Compile
     *
     * @param null $content
     * @return string
     */
    public function compile($content = null)
    {
        if (!$content) {
            return null;
        }

        if (!$this->getAllowPhp()) {
            $content = $this->escapePhp($content);
        }

        $noParse = $this->regex->extractNoParse($content);

        $content = $noParse['content'];

        $this->noParseExtractions = $noParse['extractions'];

        $setup = array(
            'name'    => 'root',
            'content' => $content,
        );

        return $this->compileRootNode($this->getBlockNodeType()->make($setup));
    }

    /**
     * Compile root node
     *
     * @param NodeInterface $node
     * @return mixed|string
     */
    public function compileRootNode(NodeInterface $node)
    {
        $view = new ViewCompiler(new StreamCompiler($node), $this);

        return $view->compile();
    }

    public function setDevelopment()
    {

    }

    public function spaces($number = 1)
    {
        return str_repeat("\x20", $number);
    }

    public function getOptimizedClass()
    {
        return $this->optimizeViewClass;
    }

    public function getCompiledViewClass()
    {
        return $this->getOptimizedClass() . $this->getCompiledView();
    }


    public function setOptimize($isOptimized = true)
    {
        $this->isOptimized = $isOptimized;
        return $this;
    }

    public function getOptimize()
    {
        return $this->isOptimized;
    }

    public function setOptimizeViewClass($optimizeViewClass = '')
    {
        $this->optimizeViewClass = $optimizeViewClass;
        return $this;
    }

    public function getOptimizeViewClass()
    {
        return $this->optimizeViewClass;
    }

    public function setCompiledView($path)
    {
        $this->path = $path;
        return $this;
    }

    public function getCompiledView()
    {
        return $this->path;
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

    /**
     * Get environment variable
     *
     * @return string
     */
    public function getLexiconVariable()
    {
        return $this->environmentVariable;
    }

    public function injectNoParse($text)
    {
        foreach ($this->noParseExtractions as $key => $extraction) {
            $extraction['content'] = addslashes($this->getRegex()->compress($extraction['content']));
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
     * Compare in conditional expression
     *
     * @param      $left
     * @param      $right
     * @param null $operator
     * @return bool
     */
    public function compare($left, $right, $operator = null)
    {
        return $this->getConditionalHandler()->compare($left, $right, $operator);
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
     * Escape PHP
     *
     * @param $content
     * @return mixed
     */
    public function escapePhp($content)
    {
        return str_replace(array('<?', '?>'), array('&lt;?', '?&gt;'), $content);
    }

    /**
     * Get allow PHP
     *
     * @return bool
     */
    public function getAllowPhp()
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

    public function getViewNamespace()
    {
        return $this->viewNamespace;
    }

    public function getViewClass($hash)
    {
        return $this->getViewNamespace().'\\View_'.$hash;
    }
}