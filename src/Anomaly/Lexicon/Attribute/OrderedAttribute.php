<?php namespace Anomaly\Lexicon\Attribute;

use Anomaly\Lexicon\Stub\LexiconStub;

class OrderedAttribute extends AttributeNode
{

    /**
     * Setup properties using the regex matches
     *
     * @return void
     */
    public function setup()
    {
        $rawAttributes = ($parent = $this->getParent()) ? $parent->getRawAttributes() : null;

        $this
            ->setValue($this->match(2))
            ->setContent($rawAttributes)
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

    /**
     * Stub for testing with PHPSpec
     *
     * @return AttributeNode|static
     */
    public static function stub()
    {
        return new static(LexiconStub::get());
    }

}