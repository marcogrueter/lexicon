<?php namespace Anomaly\Lexicon\Contract\Node;

interface RootInterface extends BlockInterface
{

    /**
     * Get footer
     *
     * @return array
     */
    public function getFooter();

    /**
     * Compile footer
     *
     * @param $source
     * @return mixed|string
     */
    public function compileFooter($source);

    /**
     * Add to footer
     *
     * @param $content
     * @return BlockInterface
     */
    public function addToFooter($content);

}