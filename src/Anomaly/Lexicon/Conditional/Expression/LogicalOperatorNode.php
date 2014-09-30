<?php namespace Anomaly\Lexicon\Conditional\Expression;

use Anomaly\Lexicon\Node\NodeType\Conditional;

/**
 * Class LogicalOperatorNode
 *
 * @package spec\Anomaly\Lexicon\Conditional\Expression
 */
class LogicalOperatorNode extends Conditional
{

    public function setup()
    {
        // This is the logical operator
        $this->setName($this->match(0));
    }

    public function compile()
    {
        return " {$this->getName()} ";
    }

} 