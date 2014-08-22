<?php namespace Anomaly\Lexicon\Contract;

interface NodeBlockInterface extends NodeInterface
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
     * Set content open
     *
     * @param $contentOpen
     * @return NodeBlockInterface
     */
    public function setContentOpen($contentOpen);

    /**
     * Set content close
     *
     * @param $contentClose
     * @return NodeBlockInterface
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