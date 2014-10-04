<?php namespace Anomaly\Lexicon\Node\NodeType;

use Anomaly\Lexicon\Contract\Node\BlockInterface;
use Anomaly\Lexicon\Contract\Node\NodeInterface;
use Anomaly\Lexicon\Contract\Node\RootInterface;
use Anomaly\Lexicon\Lexicon;
use Anomaly\Lexicon\Stub\LexiconStub;

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
    protected $openingTag = '';

    /**
     * Closing tag content
     *
     * @var string
     */
    protected $closingTag = '';

    /**
     * @var array
     */
    protected $footer = [];

    /**
     * Get regex matcher
     *
     * @return string
     */
    public function regex()
    {
        return '/(\{\{\s*(' . $this->getVariableRegex() . ')(\s.*?)\}\})(.*?)(\{\{\s*\/\2\s*\}\})/ms';
    }

    /**
     * Setup properties using the regex matches
     *
     * @return void
     */
    public function setup()
    {
        $this
            ->setFullContent($this->match(0))
            ->setOpeningTag($this->match(1))
            ->setClosingTag($this->match(5))
            ->setName($this->match(2))
            ->setRawAttributes($this->match(3))
            ->setContent($content = $this->match(4))
            ->setCurrentContent($content)
            ->setExtractionContent($content);
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
    public function getOpeningTag()
    {
        return $this->openingTag;
    }

    /**
     * Get the extraction content that closes the block
     *
     * @return string
     */
    public function getClosingTag()
    {
        return $this->closingTag;
    }

    /**
     * Set content open
     *
     * @param $openingTag
     * @return Block
     */
    public function setOpeningTag($openingTag)
    {
        $this->openingTag = $openingTag;
        return $this;
    }

    /**
     * Set content close
     *
     * @param $closingTag
     * @return BlockInterface
     */
    public function setClosingTag($closingTag)
    {
        $this->closingTag = $closingTag;
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
        return $this->compileFooter($this->compileChildren()->getCurrentContent());
    }

    /**
     * Compile footer
     *
     * @param $source
     * @return mixed|string
     */
    public function compileFooter($source)
    {
        $footer = $this->getFooter();
        if (count($footer) > 0) {
            $source = str_replace('@parent', '', $source);
            $source = ltrim($source, PHP_EOL) . PHP_EOL . implode(PHP_EOL, array_reverse($footer));
        }
        return $source;
    }

    /**
     * Compile children
     *
     * @return BlockInterface
     */
    public function compileChildren()
    {
        $nodeFactory = $this->getLexicon()->getFoundation()->getNodeFactory();
        /** @var $child NodeInterface */
        foreach ($this->getChildren() as $child) {
            if (!$child->deferCompile()) {
                $nodeFactory->inject($child, $this);
            }
        }
        foreach ($this->getChildren() as $child) {
            if ($child->deferCompile()) {
                $nodeFactory->inject($child, $this);
            }
        }
        return $this;
    }

    /**
     * Compile opening source
     *
     * @return string
     */
    public function compileOpeningTag()
    {
        if (!$this->isPhp() or $this->isFilter() or $this->isParse()) {
            return null;
        }
        return "foreach({$this->getTraversableSource()} as \$i=>{$this->getItemSource()}):";
    }

    /**
     * Compile block filter
     *
     * @return string
     */
    public function compileFilter()
    {
        $attributes = $this->compileAttributes();

        $finder = $this->getNodeFinder();

        $expected = Lexicon::EXPECTED_ECHO;

        return "echo \$__data['__env']->variable({$finder->getItemSource()},'{$finder->getName(
        )}',{$attributes},'{$this->getContent()}','','{$expected}');";
    }

    /**
     * Compile block filter
     *
     * @return string
     */
    public function compileParse()
    {
        $attributes = $this->compileAttributes();

        $finder = $this->getNodeFinder();

        $expected = Lexicon::EXPECTED_ECHO;

        $content = addslashes($this->getContent());

        return "echo \$__data['__env']->variable({$finder->getItemSource()},'{$finder->getName(
        )}',{$attributes},\$__data['__env']->parse(stripslashes('{$content}'),\$__data),'','{$expected}');";
    }

    /**
     * Compile traversable source
     *
     * @return string
     */
    public function getTraversableSource()
    {
        $attributes = $this->compileAttributes();

        $expected = Lexicon::EXPECTED_TRAVERSABLE;

        $finder = $this->getNodeFinder();

        $itemName = $finder->getItemSource();

        $name = $finder->getName();

        return "\$__data['__env']->variable({$itemName},'{$name}',{$attributes},'',[],'{$expected}')";
    }

    /**
     * Compile closing source
     *
     * @return string
     */
    public function compileClosingTag()
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

    /**
     * Stub for testing with PHPSpec
     *
     * @return Block
     */
    public static function stub()
    {
        $lexicon = LexiconStub::get();
        $factory = $lexicon->getFoundation()->getNodeFactory();
        return $factory->make(new static($lexicon))->setName('books');
    }

}