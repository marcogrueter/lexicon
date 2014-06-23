<?php namespace Aiws\Lexicon\Node;

class ConditionalEnd extends Single
{
    public $callbackEnabled = false;

    public $name = 'endif';

    public function getSetup(array $match)
    {
        $this->setExtractionContent($match[0]);
    }

    public function compile()
    {
        $hasConditionalStart = false;

        foreach ($this->getParent()->getChildren() as $node) {
            if ($node instanceof Conditional) {
                $hasConditionalStart = true;
                break;
            }
        }

        if ($hasConditionalStart) {
            return '<?php endif; ?>';
        }

        return null;
    }

}