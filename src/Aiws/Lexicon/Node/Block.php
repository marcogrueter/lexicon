<?php namespace Aiws\Lexicon\Node;

use Aiws\Lexicon\Util\Context;
use Aiws\Lexicon\Util\Type;

class Block extends Node
{
    public $fullContent = '';

    public $openContent = '';

    public $closingContent;

    public function getRegexMatcher()
    {
        return '/\{\{\s*(' . $this->lexicon->getRegex()->getVariableRegexMatcher() . ')(\s.*?)\}\}(.*?)\{\{\s*\/\1\s*\}\}/ms';
    }

    public function getMatches($text)
    {
        return $this->getOpenTagMatches($text);
    }

    public function getSetup(array $match)
    {

        $this->fullContent       = isset($match[0]) ? $match[0] : '';
        $this->name              = isset($match['name']) ? $match['name'] : $match[1];
        $this->parameters        = isset($match['parameters']) ? $match['parameters'] : isset($match[2]) ? $match[2] : null;
        $this->extractionContent = $this->content = isset($match['content']) ? $match['content'] : $match[3];

        $parts = explode($this->content, $this->fullContent);

        if (count($parts) == 2) {
            $this->openContent = $parts[0];
            $this->closingContent = $parts[1];
        }

        return $this;
    }

    public function compileParentNode($parentParsedContent)
    {
        $attributes = var_export($this->attributes, true);

        $expected = Type::ITERATEABLE;

        if ($this->parent->isRoot()) {
            $iterateableSource = "\$__lexicon->getVariable(\$__data, '{$this->name}', {$attributes}, null, [], '{$expected}')";
        } else {
            $dataSource = '$' . $this->parent->getItem();
            $iterateableSource =  "\$__lexicon->getVariable({$dataSource}, '{$this->name}', {$attributes}, null, [], '{$expected}')";
        }

        $parentParsedContent = str_replace(
            '{{ ' . $this->name . ' }}',
            "<?php foreach ({$iterateableSource} as \${$this->getItem()}): ?>",
            $parentParsedContent
        );

        $parentParsedContent = str_replace(
            '{{ /' . $this->name . ' }}',
            '<?php endforeach; ?>',
            $parentParsedContent
        );

        return $parentParsedContent;
    }

    public function getExtractionOpen()
    {
        return $this->openContent;
    }

    public function getExtractionClose()
    {
        return $this->closingContent;
    }

    public function compile()
    {
        /** @var $node Node */
        foreach ($this->children as $node) {

            // If the block is set as trash, it will be removed from the parsedContent
            if ($node->trash) {

                // Remove excessive white space so the content its easier to match
                //$this->parsedContent = $this->compress($this->parsedContent);

                $this->parsedContent = str_replace(
                    $node->getExtractionOpen() . $node->getExtractionId() . $node->getExtractionClose(),
                    '',
                    $this->parsedContent);
            } else {

                $this->inject($node);

            }
        }

        return $this->parsedContent;
    }
}