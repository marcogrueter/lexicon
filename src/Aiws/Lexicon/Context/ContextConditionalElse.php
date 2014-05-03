<?php namespace Aiws\Lexicon\Context;

class ContextConditionalElse extends ContextType
{
    public $callbackEnabled = false;

    public function getRegex()
    {
        return '/\{\{\s*else\s*\}\}/ms';
    }

    public function getSetup(array $match)
    {
        $this->name = 'else';
        $this->extractionContent = $match[0];
    }

    public function compileContext()
    {
        $hasConditionalStart = $hasConditionalEnd = false;

        foreach ($this->parent->children as $context) {
            if ($context instanceof ContextConditional) {
                $hasConditionalStart = true;
                break;
            }
        }

        foreach ($this->parent->children as $context) {
            if ($context instanceof ContextConditionalEnd) {
                $hasConditionalEnd = true;
                break;
            }
        }

        if ($hasConditionalStart and $hasConditionalEnd) {
            return $this->php('else:');
        }

        return null;
    }

}