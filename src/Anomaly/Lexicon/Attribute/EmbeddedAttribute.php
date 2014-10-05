<?php namespace Anomaly\Lexicon\Attribute;

use Anomaly\Lexicon\Lexicon;
use Anomaly\Lexicon\Stub\LexiconStub;

class EmbeddedAttribute extends AttributeNode
{

    /**
     * Attribute node types
     *
     * @var array
     */
    protected $attributeNodeTypes = [
        'Anomaly\Lexicon\Attribute\NestedNamedAttribute',
        'Anomaly\Lexicon\Attribute\NestedOrderedAttribute',
    ];

    /**
     * Setup regex match
     */
    public function setup()
    {
        $this->setContent($this->match(0));
    }

    /**
     * @return int
     */
    public function getNameAndRawAttributes()
    {
        preg_match("/\{\s*({$this->getVariableRegex()})(\s+.*?)?\s*(\/)?\}/s", $this->getContent(), $match);
        return $match;
    }

    /**
     * Get node types
     *
     * @param string $nodeGroup
     * @return array
     */
    public function getAttributeNodeTypes()
    {
        $nodeTypes = [];
        foreach ($this->attributeNodeTypes as $nodeType) {
            $nodeTypes[] = $this->getNodeFactory()->newNodeType($nodeType);
        }
        return $nodeTypes;
    }


    public function getRawAttributes()
    {
        $match = $this->getNameAndRawAttributes();
        return isset($match[2]) ? $match[2] : '';
    }

    public function getName()
    {
        $match = $this->getNameAndRawAttributes();
        return isset($match[1]) ? $match[1] : '';
    }


    /**
     * Compile php
     *
     * @return string
     */
    public function compile()
    {
        $finder = $this->getNodeFinder();
        $item = $finder->getItemSource();
        $name = $finder->getName();
        $rawAttributes = $this->getRawAttributes();
        $expected = Lexicon::EXPECTED_STRING;

        $attributes = $this
            ->setContent($rawAttributes)
            ->createChildNodes()
            ->compileSourceFromArray();

        return "\$__data['__env']->variable({$item},'{$name}',{$attributes},'',null,'{$expected}')";
    }

    /**
     * Stub for unit testing with PHPSpec
     *
     * @return AttributeNode|static
     */
    public static function stub()
    {
        return new static(LexiconStub::get());
    }

}