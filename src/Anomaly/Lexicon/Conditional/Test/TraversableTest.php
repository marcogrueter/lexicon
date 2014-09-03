<?php namespace Anomaly\Lexicon\Conditional\Test;

class TraversableTest
{

    /**
     * Check if value is in array or \IteratorAggregate
     *
     * @param $left
     * @param $right
     */
    public function in($value, $iterateable)
    {
        $in = false;

        if (is_array($iterateable) or $iterateable instanceof \IteratorAggregate) {
            foreach ($iterateable as $itemValue) {
                if ($value == $itemValue) {
                    $in = true;
                    break;
                }
            }
        }

        return $in;
    }

}
