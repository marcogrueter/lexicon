<?php namespace Anomaly\Lexicon\Attribute;

class OrderedAttribute extends AttributeNode
{

    /**
     * Setup properties using the regex matches
     *
     * @return void
     */
    public function setup()
    {
        $this
            ->setValue($this->match(2))
            ->setContent($this->getParent()->getRawAttributes())
            ->setExtractionContent($this->match(0));
    }

    /**
     * @return int|string
     */
    public function getKey()
    {
        return $this->getOffset();
    }

    /**
     * @return int|string
     */
    public function compileKey()
    {
        return $this->getOffset();
    }

    /**
     * Regex
     *
     * @return string
     */
    public function regex()
    {
        return '/\s*(\'|"|&#?\w+;)(.*?)(?<!\\\\)\1/ms';
    }

}