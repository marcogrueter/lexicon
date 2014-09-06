<?php namespace Anomaly\Lexicon\Node;



class SectionAppend extends Single
{

    /**
     * Name
     *
     * @var string
     */
    public $name = 'append';

    /**
     * Compile source
     *
     * @return string
     */
    public function compile()
    {
        return "\$__data['__env']->appendSection();";
    }

}