<?php namespace Aiws\Lexicon\Node;

class ConditionalElse extends Single
{
    public $callbackEnabled = false;

    public $name ='else';

    public function getSetup(array $match)
    {
        $this->name = 'else';
        $this->extractionContent = $match[0];
    }

    public function compile()
    {
        $hasConditionalStart = $hasConditionalEnd = false;

        foreach ($this->parent->children as $node) {
            if ($node instanceof Conditional) {
                $hasConditionalStart = true;
                break;
            }
        }

        foreach ($this->parent->children as $node) {
            if ($node instanceof ConditionalEnd) {
                $hasConditionalEnd = true;
                break;
            }
        }

        if ($hasConditionalStart and $hasConditionalEnd) {
            return '<?php else: ?>';
        }

        return null;
    }

}