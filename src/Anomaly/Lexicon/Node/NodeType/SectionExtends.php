<?php namespace Anomaly\Lexicon\Node\NodeType;


use Anomaly\Lexicon\Contract\Node\RootInterface;
use Anomaly\Lexicon\Stub\LexiconStub;

class SectionExtends extends Single
{
    /**
     * Name
     *
     * @var string
     */
    public $name = 'extends';

    /**
     * Compile source
     *
     * @return null|string
     */
    public function compile()
    {
        /** @var RootInterface $rootNode */
        $rootNode = $this->getRootNode();

        $attribute = $this->getAttributeNode()->compileAttributeValue('layout');

        if (!empty($attribute)) {
            $rootNode->addToFooter("<?php echo \$this->view()->make({$attribute},\$__data)->render(); ?>");
        }

        return null;
    }

    /**
     * @return SectionExtends
     */
    public static function stub()
    {
        $lexicon = LexiconStub::get();
        $factory = $lexicon->getFoundation()->getNodeFactory();
        $root = $factory->make(new Block($lexicon));
        return $factory->make(new static($lexicon), [], $root);
    }

}