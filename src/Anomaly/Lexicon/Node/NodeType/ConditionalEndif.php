<?php namespace Anomaly\Lexicon\Node\NodeType;

use Anomaly\Lexicon\Conditional\Validator\EndifValidator;
use Anomaly\Lexicon\Contract\Node\ConditionalEndInterface;

class ConditionalEndif extends Single
{

    protected $name = 'endif';

    /**
     * Compile source
     *
     * @return string
     */
    public function compile()
    {
        return "endif;";
    }

}