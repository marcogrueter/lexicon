<?php namespace Anomaly\Lexicon\Attribute;

use Anomaly\Lexicon\Lexicon;

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
            ->setCurrentContent($attributes = $this->match(2))
            ->setRawAttributes($attributes);
    }

    /**
     * Compile value
     *
     * @return string
     */
    public function compileValue()
    {
        $attributes = $this->compileAttributes();
        $name = $this->getValue();
        $expected = Lexicon::EXPECTED_STRING;
        return "\$__data['__env']->variable(\$__data,'{$name}',{$attributes},'',null,'{$expected}')";
    }

} 