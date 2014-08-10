<?php namespace Aiws\Lexicon\Node;



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
        return "\$__env->startSection({$this->newAttributeParser()->compileAttribute('name')});";
    }

}