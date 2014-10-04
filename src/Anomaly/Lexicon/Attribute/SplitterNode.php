<?php namespace Anomaly\Lexicon\Attribute;

use Anomaly\Lexicon\Node\NodeType\Node;
use Anomaly\Lexicon\Stub\LexiconStub;

/**
 * Class StringNode
 *
 * @package Anomaly\Lexicon\Attribute
 */
class SplitterNode extends Node
{

    /**
     * Get regex
     *
     * @return string
     */
    public function regex()
    {
        return '/(\{.+\})/ms';
    }

    /**
     * Get matches
     *
     * @return array
     */
    public function getMatches($string, $regex = null)
    {
        if (!$regex) {
            $regex = $this->regex();
        }

        return preg_split($regex, $string, -1, PREG_SPLIT_DELIM_CAPTURE);
    }

    /**
     * @return $this
     */
    public function createChildNodes()
    {
        $matches = $this->getMatches($this->getContent());
        foreach ($matches as $offset => $match) {
            $this->createChildNode($match, $offset);
        }
        return $this;
    }

    /**
     * @param $match
     * @param $offset
     */
    public function createChildNode($match, $offset)
    {
        $nodeFactory = $this->getNodeFactory();

        $nodeType = $this->detect($match)
            ? $this->getDelimiterNode()
            : $this->getSegmentNode();

        $child = $nodeFactory->make(
            $nodeType,
            [[$match]], // this needs to be like [0][0]
            $this,
            $offset,
            $this->getDepth()
        );

        $this->addChild($child);
    }

    /**
     * Detect
     *
     * @param $string
     * @return bool|int
     */
    public function detect($string)
    {
        return (bool) preg_match('/^\{.+\}$/', $string);
    }

    /**
     * Get delimiter node
     *
     * @return EmbeddedAttribute
     */
    public function getDelimiterNode()
    {
        return new EmbeddedAttribute($this->getLexicon());
    }

    /**
     * Get delimiter node
     *
     * @return EmbeddedAttribute
     */
    public function getSegmentNode()
    {
        return new StringAttribute($this->getLexicon());
    }

    /**
     * Compile
     *
     * @return string
     */
    public function compile()
    {
        $segments = [];
        /** @var AttributeNode $node */
        foreach($this->getChildren() as $node) {
            $segments[] = $node->compile();
        }
        $value = implode('.', $segments);
        return $value;
    }

    public static function stub()
    {
        return new static(LexiconStub::get());
    }

} 