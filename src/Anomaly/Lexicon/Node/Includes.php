<?php namespace Anomaly\Lexicon\Node;



class Includes extends Single
{

    /**
     * Node name
     *
     * @var string
     */
    public $name = 'include';

    /**
     * Compile source
     *
     * @return null|string
     */
    public function compile()
    {
        $attributeParser = $this->newAttributeCompiler();

        $attribute = $attributeParser->compileAttribute('partial', 0);
        $share = $attributeParser->compileNamedFromOrdered([0 => 'partial']);

        $source = null;

        if (!empty($attribute)) {
            $source = "echo \$__data['__env']->make({$attribute},array_merge(\$__data,{$share}))->render();";
        }

        return $source;
    }

}