<?php namespace Anomaly\Lexicon\Node;

use Anomaly\Lexicon\Lexicon;

/**
 * Class NodeManageable
 *
 * @package Anomaly\Lexicon\Node
 */
trait NodeManageable
{

    /**
     * Get node types
     *
     * @param string $nodeSet
     * @return array
     */
    public function getNodeTypes($nodeSet = Lexicon::DEFAULT_NODE_SET)
    {
        $nodeTypes = [];

        if (isset($this->nodeTypes[$nodeSet])) {
            foreach ($this->nodeTypes[$nodeSet] as $nodeType) {
                $nodeTypes[] = new $nodeType($this);
            }
        }

        return $nodeTypes;
    }

} 