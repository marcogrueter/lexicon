<?php namespace Anomaly\Lexicon\Conditional\Expression;

use Anomaly\Lexicon\Node\NodeType\Conditional;

/**
 * Class LogicalOperatorNode
 *
 * @package spec\Anomaly\Lexicon\Conditional\Expression
 */
class LogicalOperatorNode extends Conditional
{

    /**
     * This node is not extractable
     *
     * @var bool
     */
    protected $extractable = false;

    /**
     * Setup
     */
    public function setup()
    {
        // This is the logical operator
        $this->setContent($this->match(0));
    }

    /**
     * @return null|string
     */
    public function compile()
    {
        return " {$this->getContent()} ";
    }

} 