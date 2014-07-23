<?php namespace Aiws\Lexicon\Util;

use Aiws\Lexicon\Contract\EnvironmentInterface;

class Context
{
    protected $data;

    protected $name;

    protected $parent;

    protected $currentName;

    protected $key;

    protected $node;

    protected $source;

    protected $segments = [];

    protected $position = 1;

    protected $isRoot = false;

    protected $lexicon;

    protected $scopeGlue;

    public function __construct(EnvironmentInterface $lexicon, $data)
    {
        $this->lexicon   = $lexicon;
        $this->scopeGlue = $lexicon->getScopeGlue();
        $this->data      = $data;
    }

    /**
     * Takes a dot-notated key and finds the value for it in the given
     * array or object.
     *
     * @param  string       $key     Dot-notated key to find
     * @param  array|object $data    Array or object to search
     * @param  mixed        $default Default value to use if not found
     * @return mixed
     */
    public function getVariable($key, array $attributes = [], $content = '', $default = null, $expected = Type::ANY)
    {
        $data       = $this->getData();
        $reflection = $this->newReflection($data);

        $parts = explode($this->scopeGlue, $key);

        $pluginKey = $key;

        if ($this->lexicon->getPlugin($pluginKey)) {

            if (count($parts) > 2) {
                $plugin    = array_shift($parts);
                $method    = array_shift($parts);
                $pluginKey = $plugin . $this->scopeGlue . $method;
            }

            $data = $this->lexicon->call($pluginKey, $attributes, $content);

            $reflection = $this->newReflection($data);
        }

        $count = 0;

        foreach ($parts as $part) {

            if ($reflection->hasArrayKey($part)) {
                $data = $data[$part];
            } elseif ($reflection->hasObjectKey($part)) {
                $data = $data->{$part};
            } elseif ($reflection->hasMethod($part)) {
                $data = $data->{$part}();
            } elseif (count($parts) == $count or $this->getData() == $data) {
                $data = $default;
            }

            $reflection = $this->newReflection($data);

            $count++;
        }

        if ($expected == Type::ANY) {
            return $data;
        } elseif ($expected == Type::ECHOABLE and $reflection->isEchoable()) {
            return $data;
        } elseif ($expected == Type::ITERATEABLE and $reflection->isIteratable()) {
            return $data;
        }

        return $default;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getDataReflection()
    {
        return new Reflection($this->data);
    }

    public function getSource()
    {
        return $this->source;
    }

    public function toString()
    {
        return (string)$this->source;
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function isFirst()
    {
        return $this->position == 1;
    }

    public function isRootKey()
    {
        return ($this->isRootContext() and $this->isFirst());
    }

    public function isRootContext()
    {
        return $this->isRoot;
    }

    public function getName()
    {
        return $this->name;
    }

    public function newReflection($data)
    {
        return new Reflection($data);
    }

}