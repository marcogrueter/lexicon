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

    protected $logicalOperators = [
        'and',
        'or',
        '&&',
        '||',
    ];

    public function regex()
    {
        return '/\s*(' . implode(
            '|',
            array_walk(
                $this->getLogicalOperators(),
                function (&$value) {
                    $value = preg_quote($value);
                }
            )
        ) . ')\s*/ms';
    }

    public function getLogicalOperators()
    {
        return $this->logicalOperators;
    }
}
