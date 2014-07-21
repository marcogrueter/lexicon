<?php namespace Aiws\Lexicon\Util\Attribute;

class OrderedAttributeNode extends AttributeNode
{
    protected $isNamed = false;

    public function getSetup(array $match)
    {
        $this->setValue(isset($match[2]) ? $match[2] : '');
    }

    public function getRegexMatcher()
    {
        return '/(\'|"|&#?\w+;)\s*(.*?)\s*\1/s';
    }

    public function getKey()
    {
        return $this->getCount();
    }

}