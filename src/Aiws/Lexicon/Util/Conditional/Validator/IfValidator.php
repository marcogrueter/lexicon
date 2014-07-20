<?php namespace Aiws\Lexicon\Util\Conditional\Validator;

use Aiws\Lexicon\Util\NodeValidator;

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