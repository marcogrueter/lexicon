<?php namespace Aiws\Lexicon\Context;

class ContextVariable extends ContextType
{
    public function getRegex()
    {
        return '/\{\{\s*(' . $this->getVariableRegex() . ')(\s.*?)\}\}/m';
    }

    protected function getClosingPairRegex($name)
    {
        return '/\{\{\s*(\/' . $name . ')\s*\}\}/m';
    }

    public function getSetup(array $match)
    {
        $this->name = $match[1];
        $this->parameters = $match[2];
        $this->extractionContent = $match[0];
    }

    public function getMatches($text, $regex = null)
    {
        $matches = array();

        /**
         * $data_matches[0] is the raw data tag
         * $data_matches[1] is the data variable (dot notated)
         */
        foreach(parent::getMatches($text) as $match) {
            if (!preg_match($this->getClosingPairRegex($match[1]), $text, $closingPairMatch)) {
                $matches[] = $match;
            }
        }

        return $matches;
    }

    public function compileContext()
    {
        $dataParser = $this->data();

        if ($this->callbackHandlerPhp and $dataParser->isString($this->callbackData)) {

            return $this->php($this->callbackHandlerPhp, true);

        } else {
            $variablePHP = null;
            $value = null;

            // @todo - Check the data type of the property and decide to echo or not
            if (!empty($this->data)) {

                $isRoot = $this->parent->isRoot();

                $propertyData = $dataParser->getPropertyData($this->data, $this->name);

                if (!$isRoot and is_object($this->data)) {
                    $variablePHP = "\${$this->parent->getItem()}->{$this->name}";
                } elseif (!$isRoot and $dataParser->isArray($this->data)) {
                    $variablePHP = "\${$this->parent->getItem()}['{$this->name}']";
                } else {
                    $variablePHP = "\${$propertyData['variable']}{$propertyData['property']}";
                }
            }

            if ($dataParser->isString($propertyData['value'])) {
                return $this->php($variablePHP, true);
            }
        }

        return null;
    }

}