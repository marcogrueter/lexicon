<?php namespace Aiws\Lexicon\Util\Conditional\Validator;

use Aiws\Lexicon\Util\NodeValidator;

class ElseValidator extends NodeValidator
{

    /**
     * Is valid
     *
     * @return bool
     */
    public function isValid()
    {
        return
            $this->isEqualCount('if', 'endif');//and
            //$this->isAfterExisting('if');// and
            //$this->isAfter('elseif');
    }

}