<?php namespace Aiws\Lexicon\Util\Conditional;

use Aiws\Lexicon\Util\NodeValidator;

class ConditionalValidatorEndif extends NodeValidator
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