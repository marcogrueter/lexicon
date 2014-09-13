<?php namespace Anomaly\Lexicon\Test\Node;

use Anomaly\Lexicon\Node\Block;
use Anomaly\Lexicon\Test\LexiconTestCase;

/**
 * Class BlockTest
 *
 * @package Anomaly\Lexicon\Test\Node
 */
class BlockTest extends LexiconTestCase
{

    /**
     * Set up node
     */
    public function setUpTest()
    {
        $this->node = new Block($this->lexicon);
    }

    /**
     * Test regex matches
     */
    public function testRegexMatches()
    {
        $template = '{{ books }}<h1>{{ title }}</h1>{{ /books }}';

        $matches = $this->node->getMatches($template);

        // One match
        $this->assertCount(1, $matches);

        // The match has 4 offsets
        $this->assertCount(4, $matches[0]);

        // Offset [0][0] is the raw tag
        $this->assertEquals($template, $matches[0][0]);

        // Offset [0][1] is the tag name
        $this->assertEquals('books', $matches[0][1]);

        // Offset [0][2] is a space string
        $this->assertEquals(' ', $matches[0][2]);

        // Offset [0][2] is the content between the ignore tags
        $this->assertEquals('<h1>{{ title }}</h1>', $matches[0][3]);
    }

}
 