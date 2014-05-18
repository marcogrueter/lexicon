<?php namespace Aiws\Lexicon\Node;

class Block extends Node
{
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
        /*        if ($this->name == 'example') {
                    //dd($match);
                }*/

        $this->name              = isset($match['name']) ? $match['name'] : $match[1];
        $this->parameters        = isset($match['parameters']) ? $match['parameters'] : isset($match[2]) ? $match[2] : null;
        $this->extractionContent = $this->content = isset($match['content']) ? $match['content'] : $match[3];


        return $this;
    }

    public function compileParentNode($parentParsedContent)
    {
        $dataParser = $this->lexicon->data();

        if ($this->parent and $loopVariable = $dataParser->getVariable(
                $this->parent->data,
                $this->name
            ) and $dataParser->hasIterator($loopVariable)
        ) {

            $propertyData = $dataParser->getPropertyData($this->data, $this->name);

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

    public function compile()
    {
        foreach ($this->children as $node) {

            // If the block is set as trash, it will be removed from the parsedContent
            if ($node->trash) {

                // Remove excessive white space so the content its easier to match
                $this->parsedContent = $this->compress($this->parsedContent);

                $this->parsedContent = str_replace(
                    '{{ ' . $node->name . ' }}' . $node->getExtractionHash() . '{{ /' . $node->name . ' }}',
                    '',
                    $this->parsedContent
                );
            }

            $this->inject($node);
        }

        return $this->parsedContent;
    }
}