<?php namespace Anomaly\Lexicon\Attribute;

class OrderedAttribute extends AttributeNode
{

    /**
     * @param array $match
     * @return mixed|void
     */
    public function setup(array $match)
    {
        $this
            ->setContent($this->getParent()->getRawAttributes())
            ->setExtractionContent(isset($match[0]) ? $match[0] : null)
            ->setValue(isset($match[2]) ? $match[2] : '');
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