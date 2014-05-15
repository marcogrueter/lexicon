<?php namespace Aiws\Lexicon;

use Aiws\Lexicon\Base\Cache;
use Aiws\Lexicon\Base\Data;
use Aiws\Lexicon\Context\ContextBlock;

class Lexicon
{
    public $cachePath;

    public $disableCache = true;

    public $compress = false;

    public $scopeGlue = '.';

    public $maxDepth = 20;

    public $callback;

    protected $noParseExtractions = array();

    public $callbackHandlerClass;

    public $contexts = array(
        'Aiws\Lexicon\Context\SectionExtends',
        'Aiws\Lexicon\Context\Section',
        'Aiws\Lexicon\Context\SectionShow',
        'Aiws\Lexicon\Context\SectionEnd',
        'Aiws\Lexicon\Context\ContextBlock',
        'Aiws\Lexicon\Context\ContextConditional',
        'Aiws\Lexicon\Context\ContextConditionalElse',
        'Aiws\Lexicon\Context\ContextConditionalEnd',
        'Aiws\Lexicon\Context\ContextVariable',
    );

    public function __construct($cachePath = null, \Closure $callback = null)
    {
        $this->cachePath = $cachePath;
        $this->callback = $callback;
    }

    final public function compile($content = null, $data = null)
    {
        if (!$content) {
            return null;
        }

        $contextType = new ContextBlock;

        $content = $this->parseComments($content);
        $content = $this->extractNoParse($content);

        $setup = array(
            'name'    => '__LEXICON_ROOT__',
            'content' => $content,
        );

        $context = $contextType->make($setup);

        $context->callback = $this->callback;

        $context->data = $data;

        $parsedContext = $context->createChildContexts();

        $php = $parsedContext->compileContext();

        $php = $this->injectNoParse($php);

        // If there are any footer lines that need to get added to a template we will
        // add them here at the end of the template. This gets used mainly for the
        // template inheritance via the extends keyword that should be appended.
        if (count($parsedContext->footer) > 0)
        {
            $php = ltrim($php, PHP_EOL)
                .PHP_EOL.implode(PHP_EOL, array_reverse($parsedContext->footer));
        }

        if ($this->compress) {
            $php = $this->compress($php);
        }

        return $php;
    }

    public function parse($content = null, $data = null, $namespace = null)
    {
        $cache = $this->cache();

        if (!$this->disableCache and $cachedTemplate = $cache->get($content, $data, $namespace)) {
            return $cachedTemplate;
        }

        $cache->put($content, $namespace, $this->compile($content, $data));

        return $cache->get($content, $data, $namespace);
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

        foreach($matches as $match) {

            $extraction = array(
              'block' => $match[0],
              'hash' => '__NO_PARSE__'.md5($match[0]),
              'content' => $match[1],
            );

            $text = str_replace($extraction['block'], $extraction['hash'], $text);
            $this->noParseExtractions[] = $extraction;
        }

        return $text;
    }

    public function injectNoParse($text)
    {
        foreach($this->noParseExtractions as $key => $extraction) {
            $text = str_replace($extraction['hash'], $extraction['content'], $text);
            unset($this->noParseExtractions[$key]);
        }

        return $text;
    }
}