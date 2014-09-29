<?php namespace Anomaly\Lexicon\Conditional\Validator;

use Anomaly\Lexicon\Node\NodeValidator;

class IfValidator extends NodeValidator
{

    /**
     * Is valid
     *
     * @return bool
     */
    public function isValid()
    {
        $name = $this->getNode()->getName();

        if ($name == 'if') {

        } elseif($name == 'elseif')

        return $this->isEqualCount('if', 'endif');
    }

}