<?php namespace Anomaly\Lexicon\Node\NodeType;



use Anomaly\Lexicon\Stub\LexiconStub;

class Section extends Single
{

    /**
     * Name
     *
     * @var string
     */
    public $name = 'section';

    /**
     * Compile source
     *
     * @return string
     */
    public function compile()
    {
        $name = $this->compileAttributeValue('name');

        $source = null;

        if (!empty($name)) {
            $source = "\$this->view()->startSection({$name});";
        }

        return $source;
    }

    /**
     * Stub for testing with PHPSpec
     *
     * @return Section
     */
    public static function stub()
    {
        $lexicon = LexiconStub::get();
        $factory = $lexicon->getFoundation()->getNodeFactory();
        $node = $factory->make(new static($lexicon));
        return $node->setRawAttributes('name="foo"');
    }

}