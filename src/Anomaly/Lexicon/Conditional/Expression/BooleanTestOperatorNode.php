<?php namespace Anomaly\Lexicon\Conditional\Expression;

/**
 * Class LogicalOperatorNode
 *
 * @package spec\Anomaly\Lexicon\Conditional\Expression
 */
class BooleanTestOperatorNode extends LogicalOperatorNode
{

    /**
     * @return null|string
     */
    public function compile()
    {
        return "'{$this->getContent()}'";
    }

} 