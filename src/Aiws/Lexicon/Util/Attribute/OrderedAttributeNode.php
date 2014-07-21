<?php namespace Aiws\Lexicon\Util\Attribute;

class OrderedAttributeNode extends AttributeNode
{
    protected $isNamed = false;

    public function getSetup(array $match)
    {
        $this->setValue(isset($match[1]) ? $match[1] : '');
    }

    public function getRegexMatcher()
    {
        return '/\"\s*(.*?)\s*\"/s';
    }

    public function getKey()
    {
        return $this->getCount();
    }

}