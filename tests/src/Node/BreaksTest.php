<?php namespace Anomaly\Lexicon\Test\Node;

use Anomaly\Lexicon\Node\Block;
use Anomaly\Lexicon\Node\Breaks;
use Anomaly\Lexicon\Test\LexiconTestCase;

/**
 * Class BreaksTest
 *
 * @package Anomaly\Lexicon\Test\Node
 */
class BreaksTest extends LexiconTestCase
{

    /**
     * Set up node
     */
    public function setUpTest()
    {
        $this->node = new Breaks($this->lexicon);
    }

    /**
     * Test regex matches
     */
    public function testRegexMatches()
    {
        $template = '{{ break }}';

        $matches = $this->node->getMatches($template);

        // One match
        $this->assertCount(1, $matches);

        // The match has 3 offsets
        $this->assertCount(3, $matches[0]);

        // Offset [0][0] is the raw tag
        $this->assertEquals($template, $matches[0][0]);

        // Offset [0][1] is the tag name
        $this->assertEquals('break', $matches[0][1]);

        // Offset [0][2] is a space string
        $this->assertEquals(' ', $matches[0][2]);
    }

    /**
     * Test compiles source only if has a parent node
     */
    public function testCompilesSourceOnlyIfHasAParentNode()
    {
        $root = $this->makeBlockNode();

        $parent = $this->makeBlockNode('{{ break }}', $root);

        $node = $this->node->make([], $parent);

        $result = $node->isValid() ? $node->compile() : null;

        $this->assertEquals('break;', $result);
    }

    /**
     * Test compiles to null if it does NOT have a parent node
     */
    public function testCompilesNullIfDoesNotHaveAParentNode()
    {
        $node = $this->node->make([]);

        $result = $node->isValid() ? $node->compile() : null;

        $this->assertNull($result);
    }

}
 