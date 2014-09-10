<?php namespace Anomaly\Lexicon\Contract;

interface ExtractionInterface
{
    /**
     * Get the original content
     *
     * @return string
     */
    public function getContent();

    /**
     * Get the hash extraction id
     *
     * @return string
     */
    public function getId();

}