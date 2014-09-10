<?php namespace Anomaly\Lexicon\Conditional\Validator;

use Anomaly\Lexicon\Node\NodeValidator;

class EndifValidator extends NodeValidator
{

    /**
     * Is valid
     *
     * @return bool
     */
    public function isValid()
    {
        return
            $this->isEqualCount('if', 'endif')/* and
            $this->isAfterExisting('if') and
            $this->isAfter('elseif') and
            $this->isAfter('else')*/;
    }

}