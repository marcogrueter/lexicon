<?php namespace Anomaly\Lexicon\Contract\Attribute;

/**
 * Interface NodeInterface
 *
 * @package Anomaly\Lexicon\Contract\Attribute
 */
interface NodeInterface
{
    /**
     * Detect the strategy that should be use for parsing attributes
     *
     * @param $rawAttributes
     * @return bool
     */
    public function detect($rawAttributes);
} 