<?php namespace Anomaly\Lexicon\Node;


use Anomaly\Lexicon\Contract\Node\RootInterface;

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

        $attribute = "'test::view/layout'";// $this->newAttributeNode()->compileAttribute('layout');

        if (!empty($attribute)) {
            $rootNode->addToFooter("<?php echo \$__data['__env']->make({$attribute},\$__data)->render(); ?>");
        }

        return null;
    }

}