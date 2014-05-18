<?php namespace Aiws\Lexicon\Node;

class Variable extends Single
{
    public function getRegex()
    {
        return "/\{\{\s*(?!{static::PARENT_MATCHER})({$this->getVariableRegex()})(\s.*?)\}\}/m";
    }

    public function compile()
    {
        $dataParser = $this->lexicon->data();

        if ($this->callbackHandlerPhp and $dataParser->isString($this->callbackData)) {

            return $this->php('echo '.$this->callbackHandlerPhp);

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
                return $this->php('echo '.$variablePHP.';');
            }
        }

        return null;
    }

}