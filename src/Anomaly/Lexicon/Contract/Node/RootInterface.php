<?php namespace Anomaly\Lexicon\Contract\Node;

interface RootInterface extends BlockInterface
{

    /**
     * Get footer
     *
     * @return array
     */
    public function getFooter();
}