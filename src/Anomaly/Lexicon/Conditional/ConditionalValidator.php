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
        return $this->{'isValid' . $this->getNode()->getConstructName()}();
    }

    /**
     * Is valid if
     *
     * @return bool
     */
    public function isValidIf()
    {
        return $this->isEqualCount('if', 'endif');
    }

    /**
     * Is valid elseif
     *
     * @return bool
     */
    public function isValidElseif()
    {
        return $this->isEqualCount('if', 'endif') and $this->isAfter('if');
    }

    /**
     * Is valid else
     *
     * @return bool
     */
    public function isValidElse()
    {
        return $this->isEqualCount('if', 'endif');
    }

    /**
     * Is valid endif
     *
     * @return bool
     */
    public function isValidEndif()
    {
        return $this->isEqualCount('if', 'endif');
            /* and $this->isAfterExisting('if') and
            $this->isAfter('elseif') and
            $this->isAfter('else')*/
    }

} 