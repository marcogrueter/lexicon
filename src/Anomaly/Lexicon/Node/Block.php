<?php namespace Anomaly\Lexicon\Node;

use Anomaly\Lexicon\Contract\Node\BlockInterface;
use Anomaly\Lexicon\Contract\Node\NodeInterface;
use Anomaly\Lexicon\Contract\Node\RootInterface;
use Anomaly\Lexicon\Lexicon;

class Block extends Node implements RootInterface
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
     * This is the root node
     *
     * @var bool
     */
    protected $root = true;

    /**
     * Get regex matcher
     *
     * @return string
     */
    public function regex()
    {
        return '/\{\{\s*(' . $this->getVariableRegex(
        ) . ')(\s.*?)\}\}(.*?)\{\{\s*\/\1\s*\}\}/ms';
    }

    /**
     * Get setup
     *
     * @param array $match
     * @return BlockInterface
     */
    public function setup(array $match)
    {
        $fullContent = isset($match['fullcontent']) ? $match['fullcontent'] : null;

        $fullContent = isset($match[0]) ? $match[0] : $fullContent;

        $content = isset($match[3]) ? $match[3] : null;

        $content = isset($match['content']) ? $match['content'] : $content;

        $name = isset($match[1]) ? $match[1] : null;

        $name = isset($match['name']) ? $match['name'] : $name;

        $rawAttributes = isset($match['attributes']) ? $match['attributes'] : isset($match[2]) ? $match[2] : null;

        $this
            ->setName($name)
            ->setFullContent($fullContent)
            ->setContent($content)
            ->setExtractionContent($content)
            ->setRawAttributes($rawAttributes);

        if (isset($match['id'])) {
            $this->setId($match['id']);
        }

        $parts = [];

        if (is_string($content) and is_string($fullContent) and !empty($content) and !empty($fullContent)) {
            $parts = explode($content, $fullContent);
        }

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
     * @return BlockInterface
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
     * @return BlockInterface
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

        $this->compileChildren();

        return $this->getParsedContent();
    }

    /**
     * Compile children
     *
     * @return BlockInterface
     */
    public function compileChildren()
    {
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

        return $this;
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
        $attributes = $this->newAttributeCompiler()->compile();

        $finder = $this->getNodeFinder();

        $expected = Lexicon::ECHOABLE;

        return "echo \$__data['__env']->variable({$finder->getItemSource()}, '{$finder->getName(
        )}',{$attributes},'{$this->getContent()}','','{$expected}');";
    }

    /**
     * Compile block filter
     *
     * @return string
     */
    public function compileParse()
    {
        $attributes = $this->newAttributeCompiler()->compile();

        $finder = $this->getNodeFinder();

        $expected = Lexicon::ECHOABLE;

        $content = addslashes($this->getContent());

        return "echo \$__data['__env']->variable({$finder->getItemSource()},'{$finder->getName(
        )}',{$attributes},\$__data['__env']->parse(stripslashes('{$content}'),\$__data),'','{$expected}');";
    }

    /**
     * Compile iterateable source
     *
     * @return string
     */
    public function getIterateableSource()
    {
        $attributes = $this->newAttributeCompiler()->compile();

        $expected = Lexicon::TRAVERSABLE;

        $finder = $this->getNodeFinder();

        $itemName = $finder->getItemSource();

        $name = $finder->getName();

        return "\$__data['__env']->variable({$itemName},'{$this->getName()}',{$attributes},'',[],'{$expected}')";
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

    /**
     * Array of content to be compiled at the end of a view
     *
     * @return array
     */
    public function getFooter()
    {
        return $this->footer;
    }

    /**
     * Add to footer
     *
     * @param $content
     * @return BlockInterface
     */
    public function addToFooter($content)
    {
        $this->footer[] = $content;
        return $this;
    }

}