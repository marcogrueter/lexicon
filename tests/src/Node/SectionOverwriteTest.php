<?php namespace Anomaly\Lexicon\Test\Node;

use Anomaly\Lexicon\Node\SectionOverwrite;
use Anomaly\Lexicon\Test\LexiconTestCase;

/**
 * Class SectionOverwriteTest
 *
 * @package Anomaly\Lexicon\Test\Node
 */
class SectionOverwriteTest extends LexiconTestCase
{

    /**
     * Set up node
     */
    public function setUpTest()
    {
        $this->node = new SectionOverwrite($this->lexicon);
    }

    /**
     * Test regex matches
     */
    public function testRegexMatches()
    {
        $template = '{{ overwrite }}';

        $matches = $this->node->getMatches($template);

        // One match
        $this->assertCount(1, $matches);

        // The match has 3 offsets
        $this->assertCount(3, $matches[0]);

        // Offset [0][0] is the raw tag
        $this->assertEquals($template, $matches[0][0]);

        // Offset [0][1] is the tag name
        $this->assertEquals('overwrite', $matches[0][1]);

        // Offset [0][2] is a space string
        $this->assertEquals(' ', $matches[0][2]);
    }

    /**
     * Test compiles expected source
     */
    public function testCompilesExpectedSource()
    {
        $expected = "\$__data['__env']->stopSection(true);";

        $result = $this->node->compile();

        $this->assertEquals($expected, $result);
    }

}
 