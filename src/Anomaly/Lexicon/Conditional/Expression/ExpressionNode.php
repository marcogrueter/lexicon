<?php namespace Anomaly\Lexicon\Conditional\Expression;

use Anomaly\Lexicon\Node\NodeType\Node;

/**
 * Class ExpressionNode
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package Anomaly\Lexicon\Conditional\Expression
 */
class ExpressionNode extends Node
{

    /**
     * Get logical operators
     *
     * @var array
     */
    protected $logicalOperators = [
        'and',
        'or',
        '&&',
        '||',
    ];

    /**
     * Get regex
     *
     * @return string
     */
    public function regex()
    {
        $operators = $this->getLogicalOperators();

        foreach($operators as &$operator) {
            $operator = preg_quote($operator);
        }

        $operators = implode('|', $operators);

        return "/({$operators})/";
    }

    /**
     * Get logical operators
     *
     * @return array
     */
    public function getLogicalOperators()
    {
        return $this->logicalOperators;
    }

    /**
     * Get matches
     *
     * @return array
     */
    public function getMatches($string, $regex = null)
    {
        return $this->getSplitMatches($string, $regex);
    }

    /**
     * Get split matches
     *
     * @param      $string
     * @param null $regex
     * @return array
     */
    public function getSplitMatches($string, $regex = null)
    {
        if (!$regex) {
            $regex = $this->regex();
        }

        return preg_split($regex, $string, -1, PREG_SPLIT_DELIM_CAPTURE);
    }

}
