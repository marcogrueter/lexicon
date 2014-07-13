<?php namespace Aiws\Lexicon\Util\Conditional;

use Aiws\Lexicon\Util\NodeValidator;

class ConditionalValidatorIf extends NodeValidator
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