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
    public function getVariable($key, array $attributes = [], $content = '', $default = null, $expected = Type::ECHOABLE)
    {
        $data       = $this->getData();
        $reflection = $this->newReflection($data);

        $parts = explode($this->scopeGlue, $key);

        if ($this->lexicon->getPlugin($key)) {

            $pluginKey = $key;

            if (count($parts) > 2) {
                $plugin    = array_shift($parts);
                $method    = array_shift($parts);
                $pluginKey = $plugin . $this->scopeGlue . $method;
            }

            $data = $this->lexicon->call($pluginKey, $attributes, $content);

            if (count($parts) == 2) {
                return $data;
            }

            $reflection = $this->newReflection($data);
        }

        foreach ($parts as $part) {

            if ($reflection->hasArrayKey($part)) {
                $data = $data[$part];
            } elseif ($reflection->hasObjectKey($part)) {
                $data = $data->{$part};
            }

            $reflection = $this->newReflection($data);
        }

        if ($expected == Type::ECHOABLE and $reflection->isEchoable()) {
            return $data;
        } elseif ($expected == Type::ITERATEABLE and $reflection->isIteratable()) {
            return $data;
        }

        return $default;
    }

    /**
     * Resolved the namespaced queries gracefully.
     *
     * @param string $key
     * @return mixed
     */
    public function mehvariable($key)
    {
        /* Support [0] style array indicies */
        /*        if (preg_match("|\[[0-9]+\]|", $key))
                {
                    $key = preg_replace("|\[([0-9]+)\]|", ".$1", $key);
                }*/

        $parts = explode('.', $key);

        //$object = $this->fetch(array_shift($parts));

        /*        if (is_object($object))
                {
                    if (!method_exists($object, 'toLiquid'))
                        throw new LiquidException("Method 'toLiquid' not exists!");

                    $object = $object->toLiquid();
                }*/

        if (!is_null($object)) {
            while (count($parts) > 0) {
                /*                if ($object instanceof LiquidDrop)
                                    $object->setContext($this);*/

                $nextPartName = array_shift($parts);

                if (is_array($object)) {
                    // if the last part of the context variable is .size we just return the count
                    if ($nextPartName == 'size' && count($parts) == 0 && !array_key_exists('size', $object)) {
                        return count($object);
                    }

                    if (array_key_exists($nextPartName, $object)) {
                        $object = $object[$nextPartName];
                    } else {
                        return null;
                    }

                } elseif (is_object($object)) {
                    if ($object instanceof LiquidDrop) {
                        // if the object is a drop, make sure it supports the given method
                        if (!$object->hasKey($nextPartName)) {
                            return null;
                        }

                        // php4 doesn't support array access, so we have
                        // to use the invoke method instead
                        $object = $object->invokeDrop($nextPartName);

                    } elseif (method_exists($object, LIQUID_HAS_PROPERTY_METHOD)) {

                        if (!call_user_method(LIQUID_HAS_PROPERTY_METHOD, $object, $nextPartName)) {
                            return null;
                        }

                        $object = call_user_method(LIQUID_GET_PROPERTY_METHOD, $object, $nextPartName);


                    } else {
                        // if it's just a regular object, attempt to access a property
                        if (!property_exists($object, $nextPartName)) {
                            return null;
                        }

                        $object = $object->$nextPartName;
                    }
                }

                /*                if (is_object($object) && method_exists($object, 'toLiquid'))
                                {
                                    $object = $object->toLiquid();
                                }*/
            }

            return $object;
        } else {
            return null;
        }
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