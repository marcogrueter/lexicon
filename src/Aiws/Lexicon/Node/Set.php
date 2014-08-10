<?php namespace Aiws\Lexicon\Node;



class Set extends Single
{

    /**
     * Node name
     *
     * @var string
     */
    protected $name = 'set';

    /**
     * Compile source
     *
     * @return string
     */
    public function compile()
    {
        $source = '';

        foreach($this->newAttributeParser()->compileArray() as $key => $value) {
            if (!is_numeric($key)) {
                $source .= "\${$key} = {$value}; ";
            }
        }

        return $source;
    }

}