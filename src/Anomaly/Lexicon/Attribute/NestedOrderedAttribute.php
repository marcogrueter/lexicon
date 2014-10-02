<?php namespace Anomaly\Lexicon\Attribute;

/**
 * Class NestedOrderedAttribute
 *
 * @package Anomaly\Lexicon\Attribute
 */
class NestedOrderedAttribute extends OrderedAttribute
{

    public function setup()
    {
        $this->setValue($this->match(2));
    }

} 