<?php namespace Anomaly\Lexicon\Test\Node;

use Anomaly\Lexicon\Node\SectionAppend;
use Anomaly\Lexicon\Test\LexiconTestCase;

/**
 * Class SectionAppendTest
 *
 * @package Anomaly\Lexicon\Test\Node
 */
class SectionAppendTest extends LexiconTestCase
{

    /**
     * Set up node
     */
    public function setUpNode()
    {
        $this->node = new SectionAppend($this->lexicon);
    }

    /**
     * Test regex matches
     */
    public function testRegexMatches()
    {
        $template = '{{ append }}';

        $matches = $this->node->getMatches($template);

        // One match
        $this->assertCount(1, $matches);

        // The match has 3 items
        $this->assertCount(3, $matches[0]);

        // Offset [0][0] is the raw tag
        $this->assertEquals($template, $matches[0][0]);

        // Offset [0][1] is the tag name
        $this->assertEquals('append', $matches[0][1]);

        // Offset [0][2] is a space string
        $this->assertEquals(' ', $matches[0][2]);
    }

    public function testCompilesExpectedSource()
    {
        $expected = "\$__data['__env']->appendSection();";

        $result = $this->node->compile();

        $this->assertEquals($expected, $result);
    }

}
 