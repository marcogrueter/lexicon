<?php namespace Anomaly\Lexicon\Test\View;

/**
 * Class ObjectStub
 *
 * @package Anomaly\Lexicon\Test\View
 */
class ObjectStub implements \ArrayAccess
{
    /**
     * @var array
     */
    protected $attributes = [
        'bar' => 'BAR',
        'baz' => 'BAZ',
    ];

    /**
     * @var string
     */
    public $yay = 'YAY!';

    /**
     * @return string
     */
    public function foo()
    {
        return 'FOO';
    }

    /**
     * For testing \InvalidArgumentException
     *
     * @param array $array
     * @return null
     */
    public function method(array $array)
    {
        return null;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return '__toString result';
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists

     */
    public function offsetExists($offset)
    {
        return isset($this->attributes[$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     *
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        $value = null;

        if ($this->offsetExists($offset)) {
            $value = $this->attributes[$offset];
        }

        return $value;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->attributes[$offset] = $value;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->attributes[$offset]);
        }
    }
}