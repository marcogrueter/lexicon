<?php namespace Anomaly\Lexicon\Attribute;

use Anomaly\Lexicon\Node\Variable;
use Anomaly\Lexicon\Test\LexiconTestCase;
use PhpParser\Node\Name;

class AttributeNodeTest extends LexiconTestCase
{

    protected $variable;
    protected $block;
    protected $attributes;

    public function setUpTest()
    {
        $attributes = new AttributeNode($this->lexicon);

        $this->attributes  = $attributes->make([])
            ->setParsedContent('foo="FOO" bar="BAR"')
            ->createChildNodes();
    }

    public function testGetDetectedAttributeNodeType()
    {
        $this->assertInstanceOf('Anomaly\Lexicon\Attribute\NamedAttribute', $this->attributes->getAttributeNodeType());
    }

    public function testGetNodeTypes()
    {
        foreach ($this->attributes->getNodeTypes() as $nodeType) {
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

    public function testGetRegexMatches()
    {
        $attributeNodeType = $this->attributes->getAttributeNodeType();

        $matches = $attributeNodeType->getMatches('foo="FOO" bar="BAR"');

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
/*        $this->assertSame([
                'foo' => 'FOO',
                'bar' => 'BAR',
            ], $this->attributes->compileArray());*/
    }

    public function testCompileAttributes()
    {
        $this->assertSame("['foo' => 'FOO', 'bar' => 'BAR']", $this->attributes->compile());
    }

}
 