<?php namespace Anomaly\Lexicon\Node;



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
        $name = $this->newAttributeCompiler()->compileAttribute('name');

        $source = null;

        if (!empty($name)) {
            $source = "\$__data['__env']->startSection({$name});";
        }

        return $source;
    }

}