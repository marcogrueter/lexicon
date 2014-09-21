<?php namespace Anomaly\Lexicon\Attribute;

class NamedAttribute extends AttributeNode
{

    /**
     * Regex
     *
     * @return string
     */
    public function regex()
    {
        return '/(.*?)\s*=\s*(\'|"|&#?\w+;)(.*?)(?<!\\\\)\2/ms';
    }

}