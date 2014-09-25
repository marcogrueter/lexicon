<?php namespace Anomaly\Lexicon\Contract\View;

use Anomaly\Lexicon\Lexicon;
use Anomaly\Lexicon\Node\NodeFactory;
use Illuminate\Contracts\View\View;

interface ViewInterface extends View
{

    /**
     * Using node set
     *
     * @param string $nodeSet
     * @return mixed
     */
    public function using($nodeSet = NodeFactory::DEFAULT_NODE_GROUP);

}