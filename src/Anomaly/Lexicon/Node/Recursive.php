<?php namespace Anomaly\Lexicon\Node;

use Anomaly\Lexicon\Contract\Node\BlockInterface;

class Recursive extends Single
{
    /**
     * Node name
     *
     * @var string
     */
    protected $name = 'recursive';

    /**
     * Compile source
     *
     * @return null|string
     */
    public function compile()
    {
        if ($parent = $this->getParent() and
            $parent instanceof BlockInterface and
            $content = $parent->getFullContent() and
            !empty($content)
        ) {
            $finder = $this->getContextFinder();
            return "echo \$__data['__env']->parse('{$content}',{$finder->getItemName()});";
        }

        return null;
    }
}