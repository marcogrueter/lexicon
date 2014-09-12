<?php namespace Anomaly\Lexicon\Test\Node;

use Anomaly\Lexicon\Node\Continues;
use Anomaly\Lexicon\Test\LexiconTestCase;

/**
 * Class ContinuesTest
 *
 * @package Anomaly\Lexicon\Test\Node
 */
class ContinuesTest extends LexiconTestCase
{

    /**
     * Set up node
     */
    public function setUpNode()
    {
        $this->node = new Continues($this->lexicon);
    }

    /**
     * Test regex matches
     */
    public function testRegexMatches()
    {
        $template = '{{ continue }}';

        $matches = $this->node->getMatches($template);

        // One match
        $this->assertCount(1, $matches);

        // The match has 3 offsets
        $this->assertCount(3, $matches[0]);

        // Offset [0][0] is the raw tag
        $this->assertEquals($template, $matches[0][0]);

        // Offset [0][1] is the tag name
        $this->assertEquals('continue', $matches[0][1]);

        // Offset [0][2] is a space string
        $this->assertEquals(' ', $matches[0][2]);
    }

    /**
     * Test that Continue only compiles the source if it has a parent node that is not root
     */
    public function testCompilesSourceOnlyIfHasParent()
    {
        $template = '{{ posts }}{{ continue }}{{ /posts }}';

        $root = $this->makeBlockNode($template);

        $parent = $this->makeBlockNode($root->getContent(), $root);

        $result = $this->compileNode($this->node, $parent, $parent->getContent());

        $this->assertEquals('continue;', $result);
    }

    /**
     * Test that Continue only compiles the source if it has a parent node that is not root
     */
    public function testCompilesNullIfParentIsRoot()
    {
        $parent = $this->makeBlockNode();

        $result = $this->compileNode($this->node, $parent, $parent->getContent());

        $this->assertNull($result);
    }

    /**
     * Test that compiles null if it does noy have a parent
     */
    public function testCompilesNullIfDoesNotHaveParent()
    {
        $result = $this->compileNode($this->node, $parent = null, '{{ continue }}');

        $this->assertNull($result);
    }
}
 