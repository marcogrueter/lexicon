<?php namespace Anomaly\Lexicon\Node\NodeType;

/**
 * Class VariableUnescaped
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package Anomaly\Lexicon\Node\NodeType
 */
class VariableUnescaped extends Variable
{

    /**
     * Regex
     *
     * @return string
     */
    public function regex()
    {
        return "/\{\{\{\s*({$this->getVariableRegex()})(\s+.*?)?\s*(\/)?\}\}\}/ms";
    }

    /**
     * Compile source
     *
     * @param array $attributes
     * @return string
     */
    public function compile($echo = true, $escaped = false)
    {
        return parent::compile($echo, $escaped);
    }


}