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
        $name = $this->newAttributeParser()->compileAttribute('name');

        if (!empty($name)) {
            return "\$this->view()->startSection({$name});";
        }

        return null;
    }

}