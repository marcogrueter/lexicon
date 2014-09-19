<?php namespace Anomaly\Lexicon\Attribute;

class NamedAttribute extends AttributeNode
{
    /**
     * Regex
     */
    const REGEX = '/([a-zA-Z0-9_-]+)\s*=(\'|"|&#?\w+;)(.*?)(?<!\\\\)\2/ms';

    protected $isNamed = true;

    public function detect($rawAttributes)
    {
        return !empty($this->getMatches($rawAttributes, $this->regex()));
    }
}