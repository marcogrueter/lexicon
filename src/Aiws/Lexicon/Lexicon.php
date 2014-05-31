<?php namespace Aiws\Lexicon;

use Aiws\Lexicon\Base\Cache;
use Aiws\Lexicon\Base\Data;
use Aiws\Lexicon\Contract\EnvironmentInterface;
use Aiws\Lexicon\Contract\NodeInterface;
use Aiws\Lexicon\Contract\PluginHandlerInterface;

class Lexicon implements EnvironmentInterface
{
    public $cachePath;

    public $disableCache = true;

    public $compress = false;

    public $scopeGlue = '.';

    public $maxDepth = 20;

    const PARENT_MATCHER = 'parent';

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

        $content = $this->parseComments($content);
        $content = $this->extractNoParse($content);

        $setup = array(
            'name'    => 'root',
            'content' => $content,
        );

        return $this->compileRootNode($this->rootNodeType->make($setup), $data);
    }

    public function compileRootNode(NodeInterface $node, $data)
    {
        $node->data = $data;

        $parsedNode = $node->createChildNodes();

        $php = $parsedNode->compile();

        $php = $this->injectNoParse($php);

        // If there are any footer lines that need to get added to a template we will
        // add them here at the end of the template. This gets used mainly for the
        // template inheritance via the extends keyword that should be appended.
        if (count($parsedNode->footer) > 0) {
            $php = ltrim($php, PHP_EOL)
                . PHP_EOL . implode(PHP_EOL, array_reverse($parsedNode->footer));
        }

        if ($this->compress) {
            $php = $this->compress($php);
        }

        return $php;
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

    public function cache()
    {
        return new Cache($this->cachePath);
    }

    public function data()
    {
        return new Data($this->scopeGlue);
    }

    /**
     * Removes all of the comments from the text.
     *
     * @param  string $text Text to remove comments from
     * @return string
     */
    public function parseComments($text)
    {
        return preg_replace('/\{\{#.*?#\}\}/s', '', $text);
    }

    public function extractNoParse($text)
    {
        preg_match_all('/\{\{\s*noparse\s*\}\}(.*?)\{\{\s*\/noparse\s*\}\}/ms', $text, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {

            $extraction = array(
                'block'   => $match[0],
                'hash'    => '__NO_PARSE__' . md5($match[0]),
                'content' => $match[1],
            );

            $text                       = str_replace($extraction['block'], $extraction['hash'], $text);
            $this->noParseExtractions[] = $extraction;
        }

        return $text;
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

    public function registerPlugin($class)
    {
        $segments = explode('\\', $class);

        $shortclass = $segments[count($segments)-1];

        $name = str_replace('plugin', '', strtolower($shortclass));

        $bindString = "lexicon.plugin.{$name}";

        \App::singleton($bindString, function() use ($class) {
                return new $class;
            });

        $this->plugins[$name] = $bindString;

        return $this;
    }

    public function getPlugin($name)
    {
        $segments = explode('.', $name);

        $name = $segments[0];

        return isset($this->plugins[$name]) ? \App::make("lexicon.plugin.{$name}") : null;
    }

    public function call($name, $attributes, $content)
    {
        return $this->pluginHandler->call($name, $attributes, $content);
    }
}