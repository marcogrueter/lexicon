<?php namespace Anomaly\Lexicon\Attribute;

/**
 * Class NestedNamedAttribute
 *
 * @package Anomaly\Lexicon\Attribute
 */
class NestedNamedAttribute extends NamedAttribute
{

    public function setup()
    {
        $this->setKey($this->match(1));
        $this->setValue($this->match(3));
    }

} 