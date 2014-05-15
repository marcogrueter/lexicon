<?php namespace Aiws\Lexicon\Base;

use Aiws\Lexicon\Node\Node;

class Data
{

    public $scopeGlue;

    public function __construct($scopeGlue)
    {
        $this->scopeGlue = $scopeGlue;
    }

    public function getNodeData(Node $parentNode, $data, $key = null)
    {
        $nodeData = null;

        // Set the root of the data if this is the root
        if ($parentNode->isRoot()) {

            $nodeData = $data;

        } elseif ($this->isArray($data)
            and isset($data[$parentNode->name])
            and $this->isArray($data[$parentNode->name])
            and isset($data[$parentNode->name][$key])
        ) {

            $nodeData = $data[$parentNode->name][$key];

        } elseif (!is_numeric($key)
            and $this->isArray($data)
            and isset($data[$parentNode->name])
            and is_object($data[$parentNode->name])
            and isset($data[$parentNode->name]->$key)
        ) {

            $nodeData = $data[$parentNode->name]->$key;

        } elseif (is_object($data)
            and isset($data->{$parentNode->name})
            and ($this->isArray($data->{$parentNode->name})
                and isset($data->{$parentNode->name}[$key]))
        ) {

            $nodeData = $data->{$parentNode->name}[$key];

        } elseif ($this->isArray($data)
            and isset($data[$parentNode->name])
        ) {

            $nodeData = $data[$parentNode->name];

        } elseif (is_object($data)
            and isset($data->{$parentNode->name})
        ) {

            $nodeData = $data->{$parentNode->name};

        }

        return $nodeData;
    }


    public function isArray($data)
    {
        return (is_array($data) or $data instanceof \ArrayAccess);
    }

    public function hasIterator($data)
    {
        return (is_array($data) or $data instanceof \IteratorAggregate or $data instanceof \Iterator);
    }

    public function hasObjectKey($data, $property)
    {
        return (is_object($data)
            and (property_exists($data, $property)
                or isset($data->{$property}) or is_null($data->{$property})));
    }

    public function hasArrayKey($data, $property)
    {
        return ($this->isArray($data)
            and (isset($data[$property])));
    }

    public function getArrayValue($data, $key)
    {
        if ($this->isArray($data)) {
            return isset($data[$key]) ? $data[$key] : null;
        }

        return null;
    }

    public function getObjectValue($data, $key)
    {
        if (!is_numeric($key) and is_object($data)) {
            return isset($data->{$key}) ? $data->{$key} : null;
        }

        return null;
    }

    public function getVariable($data, $key)
    {
        if ($value = $this->getArrayValue($data, $key)) {
            return $value;
        }

        return $this->getObjectValue($data, $key);
    }

    public function getPropertyData($data, $segments = array(), $propertyData = null)
    {
        if (is_string($segments)) {
            $segments = explode($this->scopeGlue, $segments);
        }

        if (!$propertyData) {
            $propertyData = array(
                'variable'   => array_shift($segments),
                'property' => '',
                'value'    => null,
            );

            $propertyData['value'] = $this->getArrayValue($data, $propertyData['variable']);


        }

        if (!empty($segments)) {

            $key = array_shift($segments);

            if ($propertyData['value'] = $this->getObjectValue($propertyData['value'], $key)) {
                $propertyData['property'] .= "->{$key}";
            } elseif ($propertyData['value'] = $this->getArrayValue($propertyData['value'], $key)) {
                $propertyData['property'] .= "['{$key}']";
            }

            $propertyData          = $this->getPropertyData($propertyData['value'], $segments, $propertyData);
        }

        return $propertyData;
    }

    public function isString($value)
    {
        return is_string($value) or (is_object($value) and method_exists($value, '__toString'));
    }
}