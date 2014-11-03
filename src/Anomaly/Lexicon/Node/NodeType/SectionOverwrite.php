<?php namespace Anomaly\Lexicon\Node\NodeType;



class SectionOverwrite extends Single
{

    /**
     * Name
     *
     * @var string
     */
    public $name = 'overwrite';

    /**
     * Compile source
     *
     * @return string
     */
    public function compile()
    {
        return "\$this->view()->stopSection(true);";
    }

}