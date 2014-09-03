<?php namespace Anomaly\Lexicon\Node;

use Anomaly\Lexicon\Contract\NodeBlockInterface;
use Anomaly\Lexicon\Contract\NodeInterface;
use Anomaly\Lexicon\Lexicon;

class Block extends Node implements NodeBlockInterface
{

    /**
     * Full content, including opening, inner and closing
     *
     * @var string
     */
    protected $fullContent = '';

    /**
     * Opening tag content
     *
     * @var string
     */
    protected $contentOpen = '';

    /**
     * Closing tag content
     *
     * @var string
     */
    protected $contentClose = '';

    /**
     * Get regex matcher
     *
     * @return string
     */
    public function regex()
    {
        return '/\{\{\s*(' . $this->lexicon->getRegex()->getVariableRegexMatcher(
        ) . ')(\s.*?)\}\}(.*?)\{\{\s*\/\1\s*\}\}/ms';
    }

    /**
     * Get matches
     *
     * @param $text
     * @return array
     */
    public function getMatches($text)
    {
        return $this->getOpenTagMatches($text);
    }

    /**
     * Get setup
     *
     * @param array $match
     * @return $this
     */
    public function getSetup(array $match)
    {
        $fullContent = isset($match[0]) ? $match[0] : '';

        $content = isset($match['content']) ? $match['content'] : $match[3];

        $name = isset($match['name']) ? $match['name'] : $match[1];

        $parsedAttributes = isset($match['attributes']) ? $match['attributes'] : isset($match[2]) ? $match[2] : null;

        $this
            ->setName($name)
            ->setFullContent($fullContent)
            ->setContent($content)
            ->setExtractionContent($content)
            ->setParsedAttributes($parsedAttributes);

        $parts = explode($content, $fullContent);

        if (count($parts) == 2) {
            $this
                ->setContentOpen($parts[0])
                ->setContentClose($parts[1]);
        }

        return $this;
    }

    /**
     * Set full content
     *
     * @param $fullContent string
     * @return $this
     */
    public function setFullContent($fullContent)
    {
        $this->fullContent = $fullContent;
        return $this;
    }

    /**
     * Get full content
     *
     * @return string
     */
    public function getFullContent()
    {
        return $this->fullContent;
    }

    /**
     * Get the extraction content that opens the block
     *
     * @return string
     */
    public function getExtractionContentOpen()
    {
        return $this->contentOpen;
    }

    /**
     * Get the extraction content that closes the block
     *
     * @return string
     */
    public function getExtractionContentClose()
    {
        return $this->contentClose;
    }

    /**
     * Set content open
     *
     * @param $contentOpen
     * @return Block
     */
    public function setContentOpen($contentOpen)
    {
        $this->contentOpen = $contentOpen;
        return $this;
    }

    /**
     * Set content close
     *
     * @param $contentClose
     * @return NodeBlockInterface
     */
    public function setContentClose($contentClose)
    {
        $this->contentClose = $contentClose;
        return $this;
    }

    /**
     * Compile source
     *
     * @return string
     */
    public function compile()
    {
        if ($this->isFilter()) {
            return $this->compileFilter();
        } elseif ($this->isParse()) {
            return $this->compileParse();
        }

        /** @var $node NodeInterface */
        foreach ($this->getChildren() as $node) {
            if (!$node->deferCompile()) {
                $this->inject($node);
            }
        }

        foreach ($this->getChildren() as $node) {
            if ($node->deferCompile()) {
                $this->inject($node);
            }
        }

        return $this->getParsedContent();
    }

    /**
     * Compile opening source
     *
     * @return string
     */
    public function compileOpen()
    {
        if (!$this->isPhp() or $this->isFilter() or $this->isParse()) {
            return null;
        }

        return "foreach ({$this->getIterateableSource()} as \$i => \${$this->getItemName()}):";
    }

    /**
     * Compile block filter
     *
     * @return string
     */
    public function compileFilter()
    {
        $attributes = $this->newAttributeParser()->compile();

        $finder = $this->getContextFinder();

        $expected = Lexicon::ECHOABLE;

        return "echo \$__data['__env']->variable({$finder->getItemName()}, '{$finder->getName(
        )}',{$attributes},'{$this->getContent()}','','{$expected}');";
    }

    /**
     * Compile block filter
     *
     * @return string
     */
    public function compileParse()
    {
        $attributes = $this->newAttributeParser()->compile();

        $finder = $this->getContextFinder();

        $expected = Lexicon::ECHOABLE;

        $lexicon = $this->getLexicon();

        $content = addslashes($lexicon->getRegex()->compress($this->getContent()));

        return "echo \$__data['__env']->variable({$finder->getItemName()},'{$finder->getName(
        )}',{$attributes},\$__lexicon->parse(stripslashes('{$content}'),\$__data),'','{$expected}');";
    }

    /**
     * Compile iterateable source
     *
     * @return string
     */
    public function getIterateableSource()
    {
        $attributes = $this->newAttributeParser()->compile();

        $expected = Lexicon::TRAVERSABLE;

        $finder = $this->getContextFinder();

        $itemName = $finder->getItemName();

        $name = $finder->getName();

        return "\$__data['__env']->variable({$itemName},'{$name}',{$attributes},'',[],'{$expected}')";
    }

    /**
     * Compile closing source
     *
     * @return string
     */
    public function compileClose()
    {
        if (!$this->isPhp() or $this->isFilter() or $this->isParse()) {
            return null;
        }

        return "endforeach;";
    }
}