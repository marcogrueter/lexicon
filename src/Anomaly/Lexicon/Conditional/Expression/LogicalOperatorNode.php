<?php namespace Anomaly\Lexicon\Conditional\Expression;

use Anomaly\Lexicon\Node\NodeType\Conditional;

/**
 * Class LogicalOperatorNode
 *
 * @package spec\Anomaly\Lexicon\Conditional\Expression
 */
class LogicalOperatorNode extends ExpressionNode
{

    /**
     * @return null|string
     */
    public function compile()
    {
        return " {$this->getContent()} ";
    }

} 