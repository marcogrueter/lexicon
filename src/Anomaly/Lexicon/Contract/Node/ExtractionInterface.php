<?php namespace Anomaly\Lexicon\Contract\Node;

interface ExtractionInterface
{

    /**
     * Get the original content
     *
     * @return string
     */
    public function getContent();

    /**
     * Get id
     *
     * @return string
     */
    public function getId();

}