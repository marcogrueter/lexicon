<?php namespace Anomaly\Lexicon\Node\NodeType;



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
        $name = null; //$this->newAttributeNode()->compileAttribute('name');

        $source = null;

        if (!empty($name)) {
            $source = "\$__data['__env']->startSection({$name});";
        }

        return $source;
    }

}