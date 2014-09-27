<?php namespace Anomaly\Lexicon\Node\NodeType;



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
        $name = null; //$this->newAttributeNode()->compileAttribute('name');

        $source = null;

        if (!empty($name)) {
            $source = "echo \$__data['__env']->yieldContent({$name});";
        }

        return $source;
    }

}