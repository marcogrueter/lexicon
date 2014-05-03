<?php namespace Aiws\Lexicon\Context;

class ContextBlock extends ContextType
{
    public function getRegex()
    {
        return '/\{\{\s*(' . $this->getVariableRegex() . ')(\s.*?)\}\}(.*?)\{\{\s*\/\1\s*\}\}/ms';
    }

    public function getSetup(array $match)
    {
/*        if ($this->name == 'example') {
            //dd($match);
        }*/

        $this->name              = isset($match['name']) ? $match['name'] : $match[1];
        $this->parameters        = isset($match['parameters']) ? $match['parameters'] : isset($match[2]) ? $match[2] : null;
        $this->content           = isset($match['content']) ? $match['content'] : $match[3];
        $this->extractionContent = $this->content;

        return $this;
    }

    public function compileParentContext($parentParsedContent)
    {
        $dataParser = $this->data();

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

    public function compileContext()
    {
        foreach ($this->children as $context) {

            // If the block is set as trash, it will be removed from the parsedContent
            if ($context->trash) {

                // Remove excessive white space so the content its easier to match
                $this->parsedContent = $this->compress($this->parsedContent);

                $this->parsedContent = str_replace(
                    '{{ ' . $context->name . ' }}' . $context->getExtractionHash() . '{{ /' . $context->name . ' }}',
                    '',
                    $this->parsedContent
                );
            }

            $this->inject($context);
        }

        return $this->parsedContent;
    }
}