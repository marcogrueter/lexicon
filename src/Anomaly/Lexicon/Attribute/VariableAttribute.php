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
     *
     * @return string
     */
    public function regex()
    {
        return '/\{([a-zA-Z0-9_\.]+)\}/ms';
    }

} 