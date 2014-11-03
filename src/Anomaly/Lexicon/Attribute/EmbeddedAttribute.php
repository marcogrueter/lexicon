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

        $nameAndRawAttributeMatch = $this->getNameAndRawAttributes();
        $this->setName(isset($nameAndRawAttributeMatch[1]) ? $nameAndRawAttributeMatch[1] : '');
    }

    /**
     * @return int
     */
    public function getNameAndRawAttributes()
    {
        preg_match("/\{\s*({$this->getVariableRegex()})(\s+.*?)?\s*(\/)?\}/s", $this->getContent(), $nameAndRawAttributeMatch);
        return $nameAndRawAttributeMatch;
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
        $nameAndRawAttributeMatch = $this->getNameAndRawAttributes();
        return isset($nameAndRawAttributeMatch[2]) ? $nameAndRawAttributeMatch[2] : '';
    }

    /**
     * Compile php
     *
     * @return string
     */
    public function compile()
    {
        $attributes = $this
            ->setContent($this->getRawAttributes())
            ->createChildNodes()
            ->compileSourceFromArray();

        $finder = $this->getNodeFinder();
        $item = $finder->getItemSource();
        $name = $finder->getName();
        $expected = Lexicon::EXPECTED_STRING;


        return "\$this->variable({$item},'{$name}',{$attributes},'',null,'{$expected}')";
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