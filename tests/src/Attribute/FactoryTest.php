<?php namespace Anomaly\Lexicon\Attribute;

use Anomaly\Lexicon\Node\Variable;
use Anomaly\Lexicon\Test\LexiconTestCase;
use PhpParser\Node\Name;

class FactoryTest extends LexiconTestCase
{

    protected $variable;
    protected $block;
    protected $factory;

    public function setUpTest()
    {
        $this->block = $this->makeBlockNode();

        $this->block->setRawAttributes('foo="FOO" bar="BAR"');
        $this->variable = new Variable($this->lexicon);
        $this->factory  = new Factory($this->block, $this->variable);
    }

    public function testGetNodeTypes()
    {
        foreach ($this->factory->getNodeTypes() as $nodeType) {
            $this->assertInstanceOf('Anomaly\Lexicon\Contract\Node\NodeInterface', $nodeType);
        }
    }

    public function testDetectNamedAttributes()
    {
        $namedAttributes = new NamedAttribute($this->lexicon);

        $this->assertTrue($namedAttributes->detect('foo="FOO" bar="BAR"'));
    }

    public function testDetectOrderedAttributes()
    {
        $orderedAttributes = new OrderedAttribute($this->lexicon);

        $this->assertTrue($orderedAttributes->detect('"foo" "bar"'));
    }

    public function testDetectVariableAttributes()
    {
        $variableAttributes = new VariableAttribute($this->lexicon);

        $this->assertTrue($variableAttributes->detect('{foo} {bar}'));
    }

    public function testGetDetectedAttributeNodeType()
    {
        $this->assertInstanceOf('Anomaly\Lexicon\Attribute\NamedAttribute', $this->factory->getAttributeNodeType());
    }

    public function testGetRegexMatches()
    {
        $attributeNodeType = $this->factory->getAttributeNodeType();

        $matches = $attributeNodeType->getMatches($this->block->getRawAttributes());

        $expected = [
            [
                'foo="FOO"',
                'foo',
                '"',
                'FOO',
            ],
            [
                ' bar="BAR"', // some matches might have spaces that should be trimmed afterwards
                ' bar',
                '"',
                'BAR',
            ]
        ];

        $this->assertSame($expected, $matches);
    }

    public function testCreateNodes()
    {
        $this->factory->createChildNodes();

        $this->assertSame([
                'foo' => 'FOO',
                'bar' => 'BAR',
            ], $this->factory->getAttributes());
    }

}
 