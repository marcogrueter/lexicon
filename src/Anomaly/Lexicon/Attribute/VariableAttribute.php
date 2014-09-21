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
        return "/\{\s*({$this->getVariableRegex()})(\s+.*?)?\s*(\/)?\}/ms";
    }

    /**
     * Setup properties using the regex matches
     *
     * @return void
     */
    public function setup()
    {
        parent::setup();

        $this
            ->setKey($this->getOffset())
            ->setValue($this->match(1))
            ->setParsedContent($attributes = $this->match(2))
            ->setRawAttributes($attributes);
    }

    /**
     * @return string
     */
    public function compileValue()
    {
        $attributes = $this->compileAttributes();

        return "\$__data['__env']->variable(\$__data, '{$this->getValue()}', {$attributes}, '', null, 'echo')";
    }

} 