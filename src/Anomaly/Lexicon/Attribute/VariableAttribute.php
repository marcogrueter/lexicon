<?php namespace Anomaly\Lexicon\Attribute;

/**
 * Class VariableAttribute
 *
 * @package Anomaly\Lexicon\Attribute
 */
class VariableAttribute extends AttributeNode
{
    /**
     * Regex
     */
    const REGEX = '/\{([a-zA-Z0-9_\.]+)\}/ms';

    /**
     * @param $rawAttributes
     * @return bool
     */
    public function detect($rawAttributes)
    {
        return empty($this->getMatches(NamedAttribute::REGEX)) and empty($this->getMatches(OrderedAttribute::REGEX));
    }

} 