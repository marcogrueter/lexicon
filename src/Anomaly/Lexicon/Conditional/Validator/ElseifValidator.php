<?php namespace Anomaly\Lexicon\Conditional\Validator;

use Anomaly\Lexicon\Node\NodeValidator;

class ElseifValidator extends NodeValidator
{

    /**
     * Is valid
     *
     * @return bool
     */
    public function isValid()
    {
        return
            $this->isEqualCount('if', 'endif') and $this->isAfter('if');
    }

}