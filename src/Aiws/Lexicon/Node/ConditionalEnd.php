<?php namespace Aiws\Lexicon\Node;

class ConditionalEnd extends Single
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

    public function compileNode()
    {
        $hasConditionalStart = false;

        foreach ($this->parent->children as $node) {
            if ($node instanceof Conditional) {
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