<?php namespace Anomaly\Lexicon\Node;

use Anomaly\Lexicon\Lexicon;

/**
 * Class Variable
 *
 * @package Anomaly\Lexicon\Node
 */
class Variable extends Single
{

    /**
     * Regex
     *
     * @return string
     */
    public function regex()
    {
        return "/\{\{\s*({$this->getVariableRegex()})(\s+.*?)?\s*(\/)?\}\}/ms";
    }

    /**
     * Compile source
     *
     * @param array $attributes
     * @return string
     */
    public function compile()
    {
        $finder = $this->getNodeFinder();

        $item = $finder->getItemSource();

        $name = $finder->getName();

        $attributes = $this->compileAttributes();

        $expected = Lexicon::EXPECTED_ECHO;

        return "echo \$__data['__env']->variable({$item},'{$name}',{$attributes},'',null,'{$expected}');";
    }

    /**
     * Compile key
     *
     * @return int
     */
    public function compileKey()
    {
        return $this->getOffset();
    }

}