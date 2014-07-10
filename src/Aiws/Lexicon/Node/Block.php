<?php namespace Aiws\Lexicon\Node;

use Aiws\Lexicon\Util\Type;

class Block extends Node
{
    public $fullContent = '';

    public $contentOpen = '';

    public $contentClose = '';

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
        $this->setName(isset($match['name']) ? $match['name'] : $match[1]);
        $this->fullContent      = isset($match[0]) ? $match[0] : '';
        $this->parsedAttributes = isset($match['attributes']) ? $match['attributes'] : isset($match[2]) ? $match[2] : null;

        $content = isset($match['content']) ? $match['content'] : $match[3];

        $this
            ->setContent($content)
            ->setExtractionContent($content);

        $parts = explode($content, $this->fullContent);

        if (count($parts) == 2) {
            $this->contentOpen    = $parts[0];
            $this->contentClose = $parts[1];
        }

        return $this;
    }

    /**
     * Get the extraction content that opens the block
     *
     * @return string
     */
    public function getExtractionOpen()
    {
        return $this->contentOpen;
    }

    /**
     * Get the extraction content that closes the block
     *
     * @return string
     */
    public function getExtractionClose()
    {
        return $this->contentClose;
    }

    /**
     * Compile source
     *
     * @return string
     */
    public function compile()
    {
        /** @var $node Node */
        foreach ($this->getChildren() as $node) {
            // If the block is set as trash, it will be removed from the parsedContent
            if ($node->isTrashable()) {
                $this->setParsedContent(
                    str_replace(
                        $node->getExtractionId('open') . $node->getExtractionId() . $node->getExtractionId('close'),
                        '',
                        $this->getParsedContent()
                    )
                );
            } else {
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
        $iterateable = $this->getIterateableSource();

        return "<?php foreach ({$iterateable} as \${$this->getItemName()}): ?>";
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
        return '<?php endforeach; ?>';
    }
}