<?php namespace Anomaly\Lexicon\Conditional\Test;

class TraversableTest
{

    /**
     * Check if value is in array or \IteratorAggregate
     *
     * @param $value
     * @param $iterateable
     * @internal param $left
     * @internal param $right
     * @return bool
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
