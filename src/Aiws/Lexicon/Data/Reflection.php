<?php namespace Aiws\Lexicon\Data;

class Reflection
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function isArrayAccessible()
    {
        return ($this->isArray() or $this->data instanceof \ArrayAccess);
    }

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

    public function hasKey($key)
    {
        return $this->hasArrayKey($key) or $this->hasObjectKey($key);
    }

    public function hasArrayKey($key)
    {
        return ($this->isArrayAccessible() and isset($this->data[$key]));
    }

    public function hasObjectKey($key)
    {
        if ($this->isObject()) {

            return (property_exists($this->data, $key) or
                isset($this->data->{$key}) or
                is_null($this->data->{$key}));
        }

        return false;
    }

    public function getReflectionClass()
    {
        return new \ReflectionClass($this->data);
    }

    public function isArray()
    {
        return is_array($this->data);
    }

    public function isObject()
    {
        return is_object($this->data);
    }

    public function isString()
    {
        return is_string($this->data) or ($this->isObject() and method_exists($this->data, '__toString'));
    }
}