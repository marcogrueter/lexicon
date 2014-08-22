<?php namespace Anomaly\Lexicon\Conditional\Validator;

use Anomaly\Lexicon\NodeValidator;

class IfValidator extends NodeValidator
{

    /**
     * Is valid
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->isEqualCount('if', 'endif');
    }

}