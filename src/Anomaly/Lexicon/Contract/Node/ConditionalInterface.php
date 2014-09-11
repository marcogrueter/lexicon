<?php namespace Anomaly\Lexicon\Contract\Node;

interface ConditionalInterface extends NodeInterface
{

    /**
     * Get the raw expression
     *
     * @return string
     */
    public function getExpression();

    /**
     * Set expression
     *
     * @param $expression
     * @return ConditionalInterface
     */
    public function setExpression($expression);
}