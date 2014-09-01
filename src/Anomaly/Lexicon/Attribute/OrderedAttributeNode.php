<?php namespace Anomaly\Lexicon\Attribute;

class OrderedAttributeNode extends AttributeNode
{
    protected $isNamed = false;

    public function getSetup(array $match)
    {
        $this
            ->setExtractionContent(isset($match[0]) ? $match[0] : null)
            ->setValue(isset($match[2]) ? $match[2] : '');

        $this->parse();
    }

    public function regex()
    {
        return '/(\'|"|&#?\w+;)\s*(.*?)\s*\1/s';
    }

    public function getKey()
    {
        return $this->getCount();
    }

}