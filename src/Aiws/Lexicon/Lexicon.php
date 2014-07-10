<?php namespace Aiws\Lexicon;

use Aiws\Lexicon\Contract\EnvironmentInterface;
use Aiws\Lexicon\Contract\NodeInterface;
use Aiws\Lexicon\Contract\PluginHandlerInterface;
use Aiws\Lexicon\Util\ConditionalHandler;
use Aiws\Lexicon\Util\Context;
use Aiws\Lexicon\Util\Regex;
use Aiws\Lexicon\Util\Type;

class Lexicon implements EnvironmentInterface
{
    public $cachePath;

    public $disableCache = true;

    public $compress = false;

    public $scopeGlue = '.';

    public $maxDepth = 20;

    public $ignoredMatchers = [];

    protected $noParseExtractions = array();

    protected $pluginHandler;

    protected $conditionalHandler;

    protected $environmentVariable = "array_except(get_defined_vars(), array('__data', '__path'))";

    /**
     * @var NodeInterface
     */
    public $rootNodeType;

    public $nodeTypes = array();

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

    public function compile($content = null, $data = null)
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

    public function getScopeGlue()
    {
        return $this->scopeGlue;
    }

    public function getNodeTypes()
    {
        return $this->nodeTypes;
    }

    public function getMaxDepth()
    {
        return $this->maxDepth;
    }

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

    public function registerRootNodeType(NodeInterface $nodeType)
    {
        $nodeType->setEnvironment($this);
        $this->rootNodeType = $nodeType;
        return $this;
    }

    public function registerNodeType(NodeInterface $nodeType)
    {
        $nodeType->setEnvironment($this);
        $this->nodeTypes[] = $nodeType;
        return $this;
    }

    public function registerNodeTypes(array $nodeTypes)
    {
        foreach($nodeTypes as $nodeType) {
            $this->registerNodeType($nodeType);
        }
        return $this;
    }

    public function registerPlugin($name, $class)
    {
        $this->pluginHandler->register($name, $class);
        return $this;
    }

    public function getPlugin($name)
    {
        return $this->pluginHandler->get($name);
    }

    public function get($data, $key, $parameters = [], $content = '', $default = null, $expected = Type::ANY)
    {
        $context = new Context($this, $data);
        return $context->getVariable($key, $parameters, $content, $default, $expected);
    }

    public function compare($left, $right, $operator = null)
    {
        return $this->getConditionalHandler()->compare($left, $right, $operator);
    }

    public function call($name, $attributes = [], $content = '')
    {
        return $this->pluginHandler->call($name, $attributes, $content);
    }

    public function setIgnoredMatchers(array $ignoredMatchers = [])
    {
        $this->ignoredMatchers = $ignoredMatchers;
        return $this;
    }

    public function getIgnoredMatchers()
    {
        return implode('|', $this->ignoredMatchers);
    }

    public function getRootNodeType()
    {
        return $this->rootNodeType;
    }

    public function getRegex()
    {
        return $this->regex;
    }

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
}