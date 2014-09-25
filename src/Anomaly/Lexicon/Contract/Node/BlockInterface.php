<?php namespace Anomaly\Lexicon\Contract\Node;

interface BlockInterface extends NodeInterface
{

    /**
     * Set full content
     *
     * @param $fullContent
     * @return BlockInterface
     */
    public function setFullContent($fullContent);

    /**
     * Get full content
     *
     * @return string
     */
    public function getFullContent();

    /**
     * Set content open
     *
     * @param $contentOpen
     * @return BlockInterface
     */
    public function setOpeningTag($contentOpen);

    /**
     * Set content close
     *
     * @param $contentClose
     * @return BlockInterface
     */
    public function setClosingTag($contentClose);

    /**
     * Get opening tag
     *
     * @return string
     */
    public function getOpeningTag();

    /**
     * Get closing tag
     *
     * @return string
     */
    public function getClosingTag();

    /**
     * Compile opening source
     *
     * @return string
     */
    public function compileOpeningTag();

    /**
     * Compile closing source
     *
     * @return string
     */
    public function compileClosingTag();

    /**
     * @return array|\IteratorAggregate
     */
    public function getIterateableSource();

}