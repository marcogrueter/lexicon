<?php namespace Aiws\Lexicon\Util;

class Reflection
{
    /**
     * Data
     *
     * @var mixed
     */
    protected $data;

    /**
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Is array accesible
     *
     * @return bool
     */
    public function isArrayAccessible()
    {
        return $this->isArray() or $this->data instanceof \ArrayAccess;
    }

    /**
     * Is Iteratable
     *
     * @return bool
     */
    public function isIteratable()
    {
        if ($this->isArray()) {
            return true;
        }

        if ($this->isObject()) {
            $reflection = $this->getReflectionClass();
            return $reflection->isIterateable();
        }

        return false;
    }

    /**
     * Has key
     *
     * @param $key
     * @return bool
     */
    public function hasKey($key)
    {
        return $this->hasArrayKey($key) or $this->hasObjectKey($key);
    }

    /**
     * Has array key
     *
     * @param $key
     * @return bool
     */
    public function hasArrayKey($key)
    {
        return $this->isArrayAccessible() and isset($this->data[$key]);
    }

    /**
     * Has object key
     *
     * @param $key
     * @return bool
     */
    public function hasObjectKey($key)
    {
        if ($this->isObject()) {
            return property_exists($this->data, $key) or isset($this->data->{$key});
        }

        return false;
    }

    /**
     * Has method
     *
     * @param $key
     * @return bool
     */
    public function hasMethod($key)
    {
        return is_object($this->data) and method_exists($this->data, $key);
    }

    /**
     * Get reflection class
     *
     * @return \ReflectionClass
     */
    public function getReflectionClass()
    {
        return new \ReflectionClass($this->data);
    }

    /**
     * Is array
     *
     * @return bool
     */
    public function isArray()
    {
        return is_array($this->data);
    }

    /**
     * Is object
     *
     * @return bool
     */
    public function isObject()
    {
        return is_object($this->data);
    }

    /**
     * Is string
     *
     * @return bool
     */
    public function isString()
    {
        return is_string($this->data) or ($this->isObject() and method_exists($this->data, '__toString'));
    }

    /**
     * Is int
     *
     * @return bool
     */
    public function isInt()
    {
        return is_numeric($this->data);
    }

    /**
     * Is bool
     *
     * @return bool
     */
    public function isBool()
    {
        return is_bool($this->data);
    }

    /**
     * @return bool
     */
    public function isFloat()
    {
        return is_float($this->data);
    }

    /**
     * Is null
     *
     * @return bool
     */
    public function isNull()
    {
        return is_null($this->data);
    }

    /**
     * Is echoable
     *
     * @return bool
     */
    public function isEchoable()
    {
        return
            $this->isString() or
            $this->isBool() or
            $this->isInt() or
            $this->isFloat() or
            $this->isNull();
    }
}