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
        $source = null;

        if ($parent = $this->getParent() and $parent instanceof BlockInterface) {
            $finder = $this->getNodeFinder();
            $source = "echo \$__data['__env']->parse('{$parent->getFullContent()}',{$finder->getItemSource()});";
        }

        return $source;
    }
}