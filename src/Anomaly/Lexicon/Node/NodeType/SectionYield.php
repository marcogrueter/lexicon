<?php namespace Anomaly\Lexicon\Node\NodeType;



use Anomaly\Lexicon\Attribute\AttributeNode;
use Anomaly\Lexicon\Stub\LexiconStub;
use Anomaly\Lexicon\Stub\Node\NodeFinderStub;

class SectionYield extends Single
{

    /**
     * Name
     *
     * @var string
     */
    public $name = 'yield';

    /**
     * Compile source
     *
     * @return string
     */
    public function compile()
    {
        $name = $this->getAttributes()->compileAttributeValue('name');

        $source = null;

        if (!empty($name)) {
            $source = "echo \$__data['__env']->yieldContent({$name});";
        }

        return $source;
    }

    public static function stub()
    {
        $lexicon = LexiconStub::get();
        $node = new static($lexicon);
        $attributeNode = new AttributeNode($lexicon);
        $attributeNode->setRawAttributes('name="foo"');
        $node->setAttributeNode($attributeNode);
        return $node;
    }

}