<?php namespace Anomaly\Lexicon\Contract\Conditional;

use Anomaly\Lexicon\Contract\Node\ConditionalInterface;

/**
 * Class ConditionalNodeInterface
 *
 * @package Anomaly\Lexicon\Contract\Conditional
 */
interface ConditionalNodeInterface
{

    /**
     * Set expression
     *
     * @param string $expression
     * @return ConditionalInterface
     */
    public function setExpression($expression);

    /**
     * Get expression
     *
     * @return string
     */
    public function getExpression();
} 