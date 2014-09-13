<?php namespace Anomaly\Lexicon\Conditional\Test;

class TraversableTest
{

    /**
     * Check if value is in array or \IteratorAggregate
     *
     * @param $value
     * @param $traversable
     * @internal param $left
     * @internal param $right
     * @return bool
     */
    public function in($value, $traversable)
    {
        $in = false;

        if (is_array($traversable)) {

            $in = in_array($value, $traversable);

        } elseif ($traversable instanceof \IteratorAggregate) {

            foreach ($traversable as $itemValue) {

                if ($value == $itemValue) {

                    $in = true;
                    break;

                }

            }

        } elseif ($traversable instanceof \ArrayAccess) {

            $in = isset($traversable[$value]);

        } elseif (is_object($traversable)) {

            $in = isset($traversable->{$value});

        }

        return $in;
    }

}
