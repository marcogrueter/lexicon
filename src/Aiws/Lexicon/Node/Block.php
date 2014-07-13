<?php namespace Aiws\Lexicon\Node;

use Aiws\Lexicon\Contract\NodeBlockInterface;
use Aiws\Lexicon\Util\Type;

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
    public function getRegexMatcher()
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
        }

        /** @var $node Node */
        foreach ($this->getChildren() as $node) {
            $this->inject($node);
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
        if ($this->isFilter()) {
            return null;
        }

        return "<?php foreach ({$this->getIterateableSource()} as \${$this->getItemName()}): ?>";
    }

    /**
     * Compile block filter
     *
     * @return string
     */
    public function compileFilter()
    {
        $attributes = var_export($this->getAttributes(), true);

        $finder = $this->getContextFinder();

        $expected = Type::ECHOABLE;

        return "<?php echo \$__lexicon->get({$finder->getItemName()}, '{$finder->getName(
        )}', {$attributes}, '{$this->getContent()}', '', '{$expected}'); ?>";
    }

    /**
     * Compile iterateable source
     *
     * @return string
     */
    public function getIterateableSource()
    {
        $attributes = var_export($this->getAttributes(), true);

        $expected = Type::ITERATEABLE;

        $finder = $this->getContextFinder();

        $itemName = $finder->getItemName();

        $name = $finder->getName();

        return "\$__lexicon->get({$itemName}, '{$name}', {$attributes}, '', [], '{$expected}')";
    }

    /**
     * Compile closing source
     *
     * @return string
     */
    public function compileClose()
    {
        if ($this->isFilter()) {
            return null;
        }

        return '<?php endforeach; ?>';
    }
}