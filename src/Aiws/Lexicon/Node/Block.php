<?php namespace Aiws\Lexicon\Node;

use Aiws\Lexicon\Data\Context;

class Block extends Node
{
    public $fullContent = '';

    public $openContent = '';

    public $closingContent;

    public function getRegex()
    {
        return '/\{\{\s*(' . $this->getVariableRegex() . ')(\s.*?)\}\}(.*?)\{\{\s*\/\1\s*\}\}/ms';
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

        if ($this->parent and $loopVariable = $this->traversal->getVariable(
                $this->parent->data,
                $this->name
            ) and $this->traversal->hasIterator($loopVariable)
        ) {

            $propertyData = $this->traversal->getPropertyData($this->data, $this->name);

            if (!$this->parent->isRoot()) {
                $propertyData['variable'] = $this->parent->getItem();
            }

            $propertyData['variable'] .= $propertyData['property'];

            $parentParsedContent = str_replace(
                '{{ ' . $this->name . ' }}',
                $this->php(
                    "foreach (\${$propertyData['variable']} as \${$this->getItem()}):"
                ),
                $parentParsedContent
            );

            $parentParsedContent = str_replace(
                '{{ /' . $this->name . ' }}',
                $this->php('endforeach;'),
                $parentParsedContent
            );

        } else {
            $this->trash = true;
        }

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
        if ($this->isRoot()) {
            $context = new Context(
                $this->data,
                $this->name,
                '$' . $this->getItem(),
                $this->isRoot()
            );
        } else {
            $context = new Context(
                $this->data,
                $this->name
            );
        }


        /** @var $node Node */
        foreach ($this->children as $node) {

            // If the block is set as trash, it will be removed from the parsedContent
            if ($node->trash) {

                // Remove excessive white space so the content its easier to match
                //$this->parsedContent = $this->compress($this->parsedContent);

                $this->parsedContent = str_replace(
                    $node->getExtractionOpen() . $node->getExtractionHash() . $node->getExtractionClose(),
                    '',
                    $this->parsedContent);
            } else {

                $this->inject($node);

            }
        }

/*        if ($this->name == 'books') {
            dd($this->parent->parsedContent);
        }*/

        return $this->parsedContent;
    }
}