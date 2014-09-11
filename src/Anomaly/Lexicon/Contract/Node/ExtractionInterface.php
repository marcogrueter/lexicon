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
     * @param $id
     * @return NodeInterface
     */
    public function setId($id);

    /**
     * Get id
     *
     * @return string
     */
    public function getId();

}