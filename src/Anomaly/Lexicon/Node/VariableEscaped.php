<?php namespace Anomaly\Lexicon\Node;

class VariableEscaped extends Variable
{
    /**
     * Escaped regex
     *
     * @return string
     */
    public function regex()
    {
        return "/\{\{\{\s*({$this->getVariableRegex()})(\s+.*?)?\s*(\/)?\}\}\}/ms";
    }

    /**
     * Compile escaped variable
     *
     * @return string
     */
    public function compileVariable()
    {
        return 'e(' . parent::compileVariable() . ')';
    }
}