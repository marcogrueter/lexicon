<?php namespace Aiws\Lexicon;

use Aiws\Lexicon\Contract\EnvironmentInterface;
use Aiws\Lexicon\Contract\NodeInterface;
use Aiws\Lexicon\Contract\PluginHandlerInterface;
use Aiws\Lexicon\Util\Regex;

class Lexicon implements EnvironmentInterface
{
    public $cachePath;

    public $disableCache = true;

    public $compress = false;

    public $scopeGlue = '.';

    public $maxDepth = 20;

    public $ignoredMatchers = [];

    protected $noParseExtractions = array();

    public $pluginHandler;

    /**
     * @var NodeInterface
     */
    public $rootNodeType;

    public $nodeTypes = array();

    protected $plugins = array();

    public function __construct(PluginHandlerInterface $pluginHandler = null)
    {
        $this->pluginHandler  = $pluginHandler;
    }

    public function compile($content = null, $data = null)
    {
        if (!$content) {
            return null;
        }

        $regex = new Regex($this);

        $content = $regex->parseComments($content);

        $noParse = $regex->extractNoParse($content);

        $content = $noParse['content'];

        $this->noParseExtractions = $noParse['extractions'];

        $setup = array(
            'name'    => 'root',
            'content' => $content,
        );

        return $this->compileRootNode($this->rootNodeType->make($setup), $data, $regex);
    }

    public function compileRootNode(NodeInterface $node, $data, $regex)
    {
        $node->data = $data;

        $parsedNode = $node->createChildNodes();

        $source = $parsedNode->compile();

        $source = $this->injectNoParse($source);

        // If there are any footer lines that need to get added to a template we will
        // add them here at the end of the template. This gets used mainly for the
        // template inheritance via the extends keyword that should be appended.
        if (count($parsedNode->footer) > 0) {
            $source = ltrim($source, PHP_EOL)
                . PHP_EOL . implode(PHP_EOL, array_reverse($parsedNode->footer));
        }

        if ($this->compress) {
            $source = $regex->compress($source);
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

    public function injectNoParse($text)
    {
        foreach ($this->noParseExtractions as $key => $extraction) {
            $text = str_replace($extraction['hash'], $extraction['content'], $text);
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

    public function call($name, $attributes = [], $content = '')
    {
        return $this->pluginHandler->call($name, $attributes, $content);
    }

    public function setIgnoredMatchers(array $ignoredMatchers = [])
    {
        return $this->ignoredMatchers = $ignoredMatchers;
    }

    public function getIgnoredMatchers()
    {
        return implode('|', $this->ignoredMatchers);
    }

    public function getRootNodeType()
    {
        return $this->rootNodeType;
    }
}