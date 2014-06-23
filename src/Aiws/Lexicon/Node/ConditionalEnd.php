<?php namespace Aiws\Lexicon\Node;

class ConditionalEnd extends Single
{
    public $callbackEnabled = false;

    public $name = 'endif';

    public function getRegexMatcher()
    {
        return '/\{\{\s*endif\s*\}\}/ms';
    }

    public function getSetup(array $match)
    {
        $this->extractionContent = $match[0];
    }

    public function compile()
    {
        $hasConditionalStart = false;

        foreach ($this->parent->children as $node) {
            if ($node instanceof Conditional) {
                $hasConditionalStart = true;
                break;
            }
        }

        if ($hasConditionalStart) {
            //return '<?php endif; ;
        }

        return null;
    }

}