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
     * Get the regex match setup
     *
     * @param array $match
     * @return mixed|void
     */
    public function setup(array $match)
    {
        parent::setup($match);

        $this
            ->setKey($this->getOffset())
            ->setValue($this->get($match, 1));
    }

    /**
     * @return string
     */
    public function compileValue()
    {
        return "\$__data['__env']->variable(\$__data, '{$this->getValue()}', [], '', null, 'echo')";
    }

} 