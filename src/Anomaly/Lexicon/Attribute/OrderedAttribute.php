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
            ->setContent($rawAttributes = $this->getParent()->getRawAttributes())
            ->setCurrentContent($rawAttributes)
            ->setExtractionContent($this->match(0));
    }

    /**
     * Get the key
     *
     * @return int|string
     */
    public function getKey()
    {
        return $this->getOffset();
    }

    /**
     * Compile the attribute key
     *
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