<?php namespace Anomaly\Lexicon\Node\NodeType;

use Anomaly\Lexicon\Contract\Node\BlockInterface;
use Anomaly\Lexicon\Stub\LexiconStub;

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

    /**
     * Stub for testing with PHPSpec
     *
     * @return \Anomaly\Lexicon\Contract\Node\NodeInterface
     */
    public static function stub()
    {
        $lexicon = LexiconStub::get();
        $nodeFactory = $lexicon->getFoundation()->getNodeFactory();
        $parent = $nodeFactory->make(new Block($lexicon));
        $parent->setFullContent('{{ children }}');
        return $nodeFactory->make(new static($lexicon),[], $parent);
    }

}