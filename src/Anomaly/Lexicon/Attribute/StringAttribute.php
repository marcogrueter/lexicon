<?php namespace Anomaly\Lexicon\Attribute;

/**
 * Class StringAttribute
 *
 * @package Anomaly\Lexicon\Attribute
 */
class StringAttribute extends AttributeNode
{

    /**
     * Setup
     */
    public function setup()
    {
        $this->setContent($this->match(0));
    }

    /**
     * Compile
     *
     * @return string
     */
    public function compile()
    {
        return "'{$this->getContent()}'";
    }

} 