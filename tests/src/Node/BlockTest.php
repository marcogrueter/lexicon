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
        $this->assertCount(6, $matches[0]);

        // Offset [0][0] is the raw tag
        $this->assertEquals($template, $matches[0][0]);

        // Offset [0][1] is the opening tag
        $this->assertEquals('{{ books }}', $matches[0][1]);

        // Offset [0][2] is the tag name
        $this->assertEquals('books', $matches[0][2]);

        // Offset [0][2] is a space string
        $this->assertEquals(' ', $matches[0][3]);

        // Offset [0][2] is the content between the ignore tags
        $this->assertEquals('<h1>{{ title }}</h1>', $matches[0][4]);

        // Offset [0][5] is the opening tag
        $this->assertEquals('{{ /books }}', $matches[0][5]);
    }

    /**
     * Test get loop item from raw attributes
     */
    public function testGetLoopItemFromRawAttributes()
    {
        $this->node->setRawAttributes(' as post');

        $loopItem = $this->node->getLoopItemInRawAttributes();

        $this->assertSame('post', $loopItem);
    }

    /**
     * Test that the node has a plugin and the md5 method is a filter
     */
    public function testNodeHasPluginAndTheMethodIsAFilter()
    {
        $this->node->setName('test.md5');

        $this->assertTrue($this->node->isFilter());
    }

    /**
     * Test that the node has a plugin and the lowercase method is a filter
     */
    public function testNodeHasPluginAndTheMethodIsAParseAbleFilter()
    {
        $this->node->setName('test.lowercase');

        $this->assertTrue($this->node->isParse());
    }

    /**
     * Test get closing tag
     */
    public function testGetClosingTagRegex()
    {
        $this->assertEquals('/\{\{\s*(\/test)\s*\}\}/m', $this->node->getClosingTagRegex('test'));
    }

    public function testGetMatch()
    {
        $regex = '/\{\{\s*(\/test)\s*\}\}/m';
        $string = '{{ /test }}';
        $match = $this->node->getMatch($string, $regex);

        $expected = [
            '{{ /test }}',
            '/test'
        ];

        $this->assertSame($expected, $match);
    }

    /**
     * Test validator object
     */
    public function testValidator()
    {
        $this->node->setValidator(new TestValidator());
        $this->assertTrue($this->node->validate());
    }

    /**
     * Test escape
     */
    public function testEscape()
    {
        $this->assertEquals('e($var)', $this->node->escape('$var'));
    }

    /**
     * Test embedded
     */
    public function testEmbedded()
    {
        $this->node->setIsEmbedded(true);
        $this->assertTrue($this->node->isEmbedded());
    }


}
 