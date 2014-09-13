<?php namespace Anomaly\Lexicon\Test\Node;

use Anomaly\Lexicon\Node\Includes;
use Anomaly\Lexicon\Test\LexiconTestCase;

/**
 * Class IncludesTest
 *
 * @package Anomaly\Lexicon\Test\Node
 */
class IncludesTest extends LexiconTestCase
{
    /**
     * Set up node
     */
    public function setUpTest()
    {
        $this->node = new Includes($this->lexicon);
    }

    /**
     * Test regex matches
     */
    public function testRegexMatches()
    {
        $template = '{{ include "test::hello" }}';

        $matches = $this->node->getMatches($template);

        // One match
        $this->assertCount(1, $matches);

        // The match has 3 offsets
        $this->assertCount(3, $matches[0]);

        // Offset [0][0] is the raw tag
        $this->assertEquals($template, $matches[0][0]);

        // Offset [0][1] is the tag name
        $this->assertEquals('include', $matches[0][1]);

        // Offset [0][2] is a space string
        $this->assertEquals(' "test::hello"', $matches[0][2]);
    }

    public function testCompilesExpectedSource()
    {

    }

}
 