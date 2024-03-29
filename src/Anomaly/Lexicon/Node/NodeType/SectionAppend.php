<?php namespace Anomaly\Lexicon\Node\NodeType;



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
        return "\$this->view()->appendSection();";
    }

}