<?php namespace Aiws\Lexicon;

use Aiws\Lexicon\Contract\EnvironmentInterface;
use Aiws\Lexicon\Contract\NodeInterface;
use Aiws\Lexicon\Contract\PluginHandlerInterface;
use Aiws\Lexicon\Util\Conditional\ConditionalHandler;
use Aiws\Lexicon\Util\Context;
use Aiws\Lexicon\Util\Regex;
use Aiws\Lexicon\Util\Type;

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
    protected $environmentVariable = "array_except(get_defined_vars(), array('__data', '__path'))";

    /**
     * Root node type
     *
     * @var NodeInterface
     */
    public $rootNodeType;

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
     * @param Regex                  $regex
     * @param ConditionalHandler     $conditionalHandler
     * @param PluginHandlerInterface $pluginHandler
     */
    public function __construct(Regex $regex, ConditionalHandler $conditionalHandler = null, PluginHandlerInterface $pluginHandler = null)
    {
        $this->regex = $regex;
        $this->conditionalHandler  = $conditionalHandler;
        $this->pluginHandler  = $pluginHandler;
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

        $content = $this->regex->parseComments($content);

        $noParse = $this->regex->extractNoParse($content);

        $content = $noParse['content'];

        $this->noParseExtractions = $noParse['extractions'];

        $setup = array(
            'name'    => 'root',
            'content' => $content,
        );

        return $this->compileRootNode($this->rootNodeType->make($setup));
    }

    /**
     * Compile root node
     *
     * @param NodeInterface $node
     * @return mixed|string
     */
    public function compileRootNode(NodeInterface $node)
    {
        $parsedNode = $node->createChildNodes();

        $source = $parsedNode->compile();

        $source = $this->injectNoParse($source);

        // If there are any footer lines that need to get added to a template we will
        // add them here at the end of the template. This gets used mainly for the
        // template inheritance via the extends keyword that should be appended.
        if (count($parsedNode->getFooter()) > 0) {
            $source = ltrim($source, PHP_EOL)
                . PHP_EOL . implode(PHP_EOL, array_reverse($parsedNode->getFooter()));
        }

        if ($this->compress) {
            $source = $this->regex->compress($source);
        }

        return $source;
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
        return $this->pluginHandler->setEnvironment($this);
    }

    /**
     * @return ConditionalHandler|mixed
     */
    public function getConditionalHandler()
    {
        return $this->conditionalHandler;
    }

    /**
     * Set environment variable
     *
     * @param $environmentVariable
     * @return $this
     */
    public function setEnvironmentVariable($environmentVariable)
    {
        $this->environmentVariable = $environmentVariable;
        return $this;
    }

    /**
     * Get environment variable
     *
     * @return string
     */
    public function getEnvironmentVariable()
    {
        return $this->environmentVariable;
    }

    public function injectNoParse($text)
    {
        foreach ($this->noParseExtractions as $key => $extraction) {
            $text = str_replace($extraction['id'], $extraction['content'], $text);
            unset($this->noParseExtractions[$key]);
        }

        return $text;
    }

    /**
     * Register root node type
     *
     * @param NodeInterface $nodeType
     * @return EnvironmentInterface
     */
    public function registerRootNodeType(NodeInterface $nodeType)
    {
        $nodeType->setEnvironment($this);
        $this->rootNodeType = $nodeType;
        return $this;
    }

    /**
     * Register node type
     *
     * @param NodeInterface $nodeType
     * @return EnvironmentInterface
     */
    public function registerNodeType(NodeInterface $nodeType)
    {
        $nodeType->setEnvironment($this);
        $this->nodeTypes[] = $nodeType;
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
        foreach($nodeTypes as $nodeType) {
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
     * Get variable
     *
     * @param             $data
     * @param             $key
     * @param array       $parameters
     * @param string      $content
     * @param null        $default
     * @param null|string $expected
     * @return mixed
     */
    public function get($data, $key, $parameters = [], $content = '', $default = null, $expected = Type::ANY)
    {
        $context = new Context($this, $data);
        return $context->getVariable($key, $parameters, $content, $default, $expected);
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
    public function getRootNodeType()
    {
        return $this->rootNodeType;
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
        if ($plugin = $this->getPluginHandler()->get($name)) {
            $segments = explode('.', $name);
            return $plugin->isFilter($segments[1]);
        }

        return false;
    }
}