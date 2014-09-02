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
        $attributeParser = $this->newAttributeParser();

        $attribute = $attributeParser->compileAttribute('partial', 0);
        $share = $attributeParser->compileNamedFromOrdered([0 => 'partial']);

        if (!empty($attribute)) {
            return "echo \$this->view()->make({$attribute},array_except(array_merge(get_defined_vars(),{$share}),array('__data','__path')))->render();";
        }

        return null;
    }

}