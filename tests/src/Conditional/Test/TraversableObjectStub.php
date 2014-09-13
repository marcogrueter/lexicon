<?php namespace Anomaly\Lexicon\Test\Conditional\Test;

/**
 * Class TraversableObjectStub
 *
 * @package Anomaly\Lexicon\Test\Conditional\Test
 */
class TraversableObjectStub implements \IteratorAggregate
{

    /**
     * @var string
     */
    public $one = 'one';

    /**
     * @var string
     */
    public $two = 'two';

    /**
     * @var string
     */
    public $three = 'three';

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this);
    }

}