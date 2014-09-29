<?php namespace Anomaly\Lexicon\Conditional\Test;

class ItemTest
{

    /**
     * Check if value is in array
     *
     * @param $value
     * @param $array
     * @return bool
     */
    public function in($value, $array)
    {
        return is_array($array) and in_array($value, $array);
    }

    /**
     * Check if object has property
     *
     * @param $object
     * @param $value
     * @return bool
     */
    public function has($object, $property)
    {
        $has = false;

        if (is_array($object) or $object instanceof \ArrayAccess) {

            $has = isset($object[$property]);

        } elseif (is_object($object)) {

            $has = property_exists($object, $property) or method_exists($object, $property);

        }

        return $has;
    }

}
