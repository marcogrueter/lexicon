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
        $name = $this->newAttributeParser()->compileAttribute('name');

        if (!empty($name)) {
            return "echo \$this->view()->yieldContent({$name});";
        }

        return null;
    }

}