<?php namespace Anomaly\Lexicon\Stub;

use IteratorAggregate;


/**
 * Class TraversableObject
 *
 * @package Anomaly\Lexicon\Stub
 */
class TraversableObject implements IteratorAggregate
{
    public function getIterator()
    {
        return new \ArrayIterator([]);
    }
} 