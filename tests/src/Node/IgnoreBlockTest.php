<?php namespace Anomaly\Lexicon\Test\Node;

use Anomaly\Lexicon\Node\IgnoreBlock;
use Anomaly\Lexicon\Test\LexiconTestCase;

/**
 * Class IgnoreBlockTest
 *
 * @package Anomaly\Lexicon\Test\Node
 */
class IgnoreBlockTest extends LexiconTestCase
{

    /**
     * Set up node
     */
    public function setUpTest()
    {
        $this->node = new IgnoreBlock($this->lexicon);
    }

    /**
     * Test regex matches
     */
    public function testRegexMatches()
    {
        $template = '{{ ignore }}{{ tag }}{{ /ignore }}';

        $matches = $this->node->getMatches($template);

        // One match
        $this->assertCount(1, $matches);

        // The match has 4 offsets
        $this->assertCount(4, $matches[0]);

        // Offset [0][0] is the raw tag
        $this->assertEquals($template, $matches[0][0]);

        // Offset [0][1] is the tag name
        $this->assertEquals('ignore', $matches[0][1]);

        // Offset [0][2] is a space string
        $this->assertEquals(' ', $matches[0][2]);

        // Offset [0][2] is the content between the ignore tags
        $this->assertEquals('{{ tag }}', $matches[0][3]);
    }

    /**
     * Test renders raw tag without parsing it
     */
    public function testRendersTagWithoutParsingIt()
    {
        $template = '{{ ignore }}{{ tag }}{{ /ignore }}';

        $expected = '{{ tag }}';

        $result = $this->compileNode($this->node, $parent = null, $template);

        $this->assertEquals($expected, $result);
    }
}
 