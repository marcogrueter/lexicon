<?php namespace Aiws\Lexicon;

use Aiws\Lexicon\Contract\EnvironmentInterface;
use Aiws\Lexicon\Contract\NodeInterface;
use Aiws\Lexicon\Contract\PluginHandlerInterface;
use Aiws\Lexicon\Conditional\ConditionalHandler;
use Aiws\Lexicon\Conditional\Test\StringTest;

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
    protected $environmentVariable = "array_except(get_defined_vars(),array('__data','__path'))";

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
    protected $optimizeViewClass = 'AiwsLexiconView__';

    /**
     * @var bool
     */
    protected $development = false;

    /**
     * @var array
     */
    protected $parsePaths = [];

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

        $compiledSource = $parsedNode->compile();

        $source = '';

        $stringTest = new StringTest();

        foreach (explode("\n", $compiledSource) as $line) {

            if (!empty($line)) {
                if (!$stringTest->startsWith($line, '__COMPILED__')) {
                    $line = $this->compileStringLine($line);
                } else {
                    $line = $this->compileLine($line);
                }

                $source .= $this->spaces(8) . $line . "\n";
            }
        }

        $source = str_replace("\n\n", "\n", $source);

        $source = $this->injectNoParse($source);

        // If there are any footer lines that need to get added to a template we will
        // add them here at the end of the template. This gets used mainly for the
        // template inheritance via the extends keyword that should be appended.

        $footer = $parsedNode->getFooter();

        if (count($footer) > 0) {

            foreach ($footer as &$line) {
                $line = $this->compileLine($line);
                if ($this->getOptimize()) {
                    $line = $this->spaces(8) . $line . "\n";
                }
            }

            $source = ltrim($source, PHP_EOL) . PHP_EOL . implode(PHP_EOL, array_reverse($footer));
        }

        return $this->compileView($source);
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

    public function compileView($source)
    {
        if (!$this->getOptimize()) {
            return $source;
        }

        $view = '<?php class ';

        $view .= $this->getCompiledViewClass() . "\n";

        $view .= "{\n";

        $view .= "{$this->spaces(4)}public function render(\$__data) {\n\n";

        $view .= "{$this->spaces(8)}extract(\$__data);\n\n";

        $view .= $source . "\n";

        $view .= "{$this->spaces(4)}}\n";

        $view .= "}" . PHP_EOL;

        return $view;
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

    public function removeCompiledPrefix($line)
    {
        return $line = str_replace('__COMPILED__', '', $line);
    }

    public function compileLine($line)
    {
        $line = $this->removeCompiledPrefix($line);

        if ($this->getOptimize()) {
            return $line;
        }

        if (!empty($line)) {
            return "<?php {$line} ?>";
        }

        return null;
    }

    public function compileStringLine($line)
    {
        $line = $this->removeCompiledPrefix($line);

        if ($this->getOptimize()) {

            $stringTest = new StringTest();

            if ($stringTest->contains($line, "'")) {
                $line = addslashes($line);
                return "echo stripslashes('{$line}');";
            } else {
                return "echo '{$line}';";
            }
        }

        return $line;
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
            $extraction['content'] = addslashes($this->getRegex()->compress($extraction['content']));
            $text                  = str_replace($extraction['id'], $extraction['content'], $text);
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
     * Takes a dot-notated key and finds the value for it in the given
     * array or object.
     *
     * @param  string       $key     Dot-notated key to find
     * @param  array|object $data    Array or object to search
     * @param  mixed        $default Default value to use if not found
     * @return mixed
     */
    public function get($data, $key, array $attributes = [], $content = '', $default = null, $expected = Expected::ANY)
    {
        $scopes = $pluginScopes = explode($this->scopeGlue, $key);

        $pluginKey = $key;

        $original = $data;

        if ($this->pluginHandler->get($pluginKey)) {

            $plugin = array_shift($scopes);
            $method = array_shift($scopes);

            if (count($scopes) > 2) {
                $pluginKey = $plugin . $this->scopeGlue . $method;
            }

            $data = $this->pluginHandler->call($pluginKey, $attributes, $content);
        }

        $previousScope = null;
        $invalidScope  = null;

        while (count($scopes) > 0) {

            $scope = array_shift($scopes);

            if (is_object($data) and method_exists($data, $scope)) {
                try {
                    $data = call_user_func_array([$data, $scope], $attributes);
                } catch (\InvalidArgumentException $e) {
                    echo "There is a problem with the <b>{$key}</b> variable. One of the attributes maybe incorrect.";
                    // @todo - log exception
                    // @todo - fire exception event
                } catch (\ErrorException $e) {
                    echo "There is a problem with the <b>{$key}</b> variable. One of the attributes maybe incorrect.";
                    // @todo - log exception
                    // @todo - fire exception event
                } catch (\Exception $e) {
                    echo "There is a problem with the <b>{$key}</b> variable.";
                    // @todo - log exception
                    // @todo - fire exception event
                }
            } elseif ((is_array($data) or $data instanceof \ArrayAccess) and isset($data[$scope])) {
                $data = $data[$scope];
            } elseif (is_object($data) and isset($data->{$scope})) {
                $data = $data->{$scope};
            } elseif (empty($scopes) and
                $scope == 'count' and
                (!$invalidScope or $previousScope != $invalidScope) and
                (is_array($data) or $data instanceof \Countable or is_string($data))
            ) {
                if (is_string($data)) {
                    $data = strlen($data);
                } else {
                    $data = count($data);
                }
            } elseif (empty($scopes) or $data == $original) {
                $data = $default;
            } else {
                $invalidScope = $scope;
            }

            $previousScope = $scope;
        }

        if ($expected == Expected::ANY) {
            return $data;
        } elseif ($expected == Expected::ECHOABLE and
            (is_string($data) or is_numeric($data) or is_bool($data) or is_null($data) or is_float($data) or
                (is_object($data) and method_exists($data, '__toString')))
        ) {
            return $data;
        } elseif ($expected == Expected::TRAVERSABLE and is_array($data) or $data instanceof \Traversable) {
            return $data;
        }

        return $default;
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

}