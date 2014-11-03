<?php namespace Anomaly\Lexicon\Node\NodeType;



class SectionShow extends Single
{

    /**
     * Name
     *
     * @var string
     */
    public $name = 'show';

    /**
     * Compile source
     *
     * @return string
     */
    public function compile()
    {
        return "echo \$this->view()->yieldSection();";
    }

}