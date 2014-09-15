<?php namespace Anomaly\Lexicon\Contract\Node;

interface BlockInterface extends NodeInterface
{

    /**
     * Get the extraction content that opens the block
     *
     * @return string
     */
    public function getExtractionContentOpen();

    /**
     * Get the extraction content that closes the block
     *
     * @return string
     */
    public function getExtractionContentClose();

    /**
     * Set full content
     *
     * @param $fullContent
     * @return BlockInterface
     */
    public function setFullContent($fullContent);

    /**
     * Set open and closing content
     *
     * @param $content
     * @param $fullContent
     * @return BlockInterface
     */
    public function setOpenAndClose($content, $fullContent);

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
    public function setContentOpen($contentOpen);

    /**
     * Set content close
     *
     * @param $contentClose
     * @return BlockInterface
     */
    public function setContentClose($contentClose);

    /**
     * Compile opening source
     *
     * @return string
     */
    public function compileOpen();

    /**
     * Compile closing source
     *
     * @return string
     */
    public function compileClose();

    /**
     * @return array|\IteratorAggregate
     */
    public function getIterateableSource();

}