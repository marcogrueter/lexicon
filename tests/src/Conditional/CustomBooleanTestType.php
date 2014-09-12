<?php namespace Anomaly\Lexicon\Test\Conditional;

/**
 * Class CustomBooleanTestType
 *
 * @package Anomaly\Lexicon\Test\Conditional
 */
class CustomBooleanTestType
{
    /**
     * Is cooler than boolean test
     *
     * @param $left
     * @param $right
     * @return bool
     */
    public function isCoolerThan($left, $right)
    {
        return true;
    }
}