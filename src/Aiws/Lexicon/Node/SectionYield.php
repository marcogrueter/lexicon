<?php namespace Aiws\Lexicon\Node;



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
        return "echo \$__env->yieldContent({$this->newAttributeParser()->compileAttribute('name')});";
    }

}