<?php namespace Anomaly\Lexicon\Node\NodeType;



class SectionStop extends Single
{

    /**
     * Name
     *
     * @var string
     */
    public $name = 'stop';

    /**
     * Compile source
     *
     * @return string
     */
    public function compile()
    {
        return "\$__data['__env']->stopSection();";
    }

}