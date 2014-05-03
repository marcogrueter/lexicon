<?php namespace Aiws\Lexicon\Context;

class ContextConditionalEnd extends ContextType
{
    public $callbackEnabled = false;

    public function getRegex()
    {
        return '/\{\{\s*endif\s*\}\}/ms';
    }

    public function getSetup(array $match)
    {
        $this->name = 'endif';
        $this->extractionContent = $match[0];
    }

    public function compileContext()
    {
        $hasConditionalStart = false;

        foreach ($this->parent->children as $context) {
            if ($context instanceof ContextConditional) {
                $hasConditionalStart = true;
                break;
            }
        }

        if ($hasConditionalStart) {
            return $this->php('endif;');
        }

        return null;
    }

}