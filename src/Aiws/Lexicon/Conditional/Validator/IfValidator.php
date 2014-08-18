<?php namespace Aiws\Lexicon\Conditional\Validator;

use Aiws\Lexicon\NodeValidator;

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