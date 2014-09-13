<?php namespace Anomaly\Lexicon\Node;


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
        $rootNode = $this->getRootNode();

        $attribute = $this->newAttributeCompiler()->compileAttribute('layout');

        if (!empty($attribute)) {
            $rootNode->footer[] = $this->php(
                "echo \$__data['__env']->make({$attribute},\$__data)->render();"
            );
        }

        return null;
    }

}