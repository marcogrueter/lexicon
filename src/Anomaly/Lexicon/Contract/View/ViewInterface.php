<?php namespace Anomaly\Lexicon\Contract\View;

use Anomaly\Lexicon\Lexicon;
use Illuminate\Contracts\View\View;

interface ViewInterface extends View
{

    /**
     * Using node set
     *
     * @param string $nodeSet
     * @return mixed
     */
    public function using($nodeSet = Lexicon::DEFAULT_NODE_SET);

}