<?php namespace Anomaly\Lexicon\Contract\Attribute;

use Anomaly\Lexicon\Contract\Node\NodeInterface as BaseNodeInterface;

/**
 * Interface NodeInterface
 *
 * @package Anomaly\Lexicon\Contract\Attribute
 */
interface NodeInterface extends BaseNodeInterface
{
    /**
     * Detect the strategy that should be use for parsing attributes
     *
     * @param $rawAttributes
     * @return bool
     */
    public function detect($rawAttributes);
} 