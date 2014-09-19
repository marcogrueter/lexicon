<?php namespace Anomaly\Lexicon\Attribute;

class OrderedAttribute extends AttributeNode
{
    const REGEX = '/(\'|"|&#?\w+;)\s*(.*?)\s*\1/ms';

    protected $isNamed = false;

    public function setup(array $match)
    {
        $this
            ->setExtractionContent(isset($match[0]) ? $match[0] : null)
            ->setValue(isset($match[2]) ? $match[2] : '');

        $this->parse();
    }

    public function detect($rawAttributes)
    {
        return !empty($this->getMatches($rawAttributes, $this->regex()));
    }

    public function regex()
    {
        return self::REGEX;
    }

    public function getKey()
    {
        return $this->getCount();
    }

}