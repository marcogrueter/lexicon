<?php namespace Anomaly\Lexicon\Node;



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
        $name = $this->newAttributeCompiler()->compileAttribute('name');

        $source = null;

        if (!empty($name)) {
            $source = "echo \$__data['__env']->yieldContent({$name});";
        }

        return $source;
    }

}