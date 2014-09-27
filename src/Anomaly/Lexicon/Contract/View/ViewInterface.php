<?php namespace Anomaly\Lexicon\Contract\View;

use Anomaly\Lexicon\Node\NodeFactory;
use Illuminate\Contracts\View\View;

interface ViewInterface extends View
{

    /**
     * Using node set
     *
     * @param string $nodeGroup
     * @return mixed
     */
    public function using($nodeGroup = NodeFactory::DEFAULT_NODE_GROUP);

}