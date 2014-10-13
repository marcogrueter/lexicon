<?php namespace Anomaly\Lexicon\Conditional;

use Anomaly\Lexicon\Contract\Node\ConditionalInterface;
use Anomaly\Lexicon\Node\NodeValidator;

/**
 * Class ConditionalValidator
 *
 * @package Anomaly\Lexicon\Conditional
 */
class ConditionalValidator extends NodeValidator
{

    /**
     * Is valid
     *
     * @return bool
     */
    public function isValid()
    {
        return true; //$this->{'validate' . $this->getNode()->getConstructName()}();
    }

    public function validate()
    {
        return true;
    }


} 