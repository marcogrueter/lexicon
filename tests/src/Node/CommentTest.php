<?php namespace Anomaly\Lexicon\Test\Node;

use Anomaly\Lexicon\Node\Comment;
use Anomaly\Lexicon\Test\LexiconTestCase;

/**
 * Class SingleNullTest
 *
 * @package Anomaly\Lexicon\Test\Node
 */
class CommentTest extends LexiconTestCase
{



    /**
     * Set up node
     */
    public function setUpTest()
    {
        $this->node = new Comment($this->lexicon);
    }

    /**
     * Test regex matches
     */
    public function testRegexMatches()
    {
        $template = '{{-- This comment will not be rendered --}}';

        $matches = $this->node->getMatches($template);
    }

    /**
     * Comments should always compile to null regardless of any other factors
     */
    public function testCommentCompilesToNull()
    {
        $result = $this->node->make([])->compile();

        $this->assertNull($result);
    }

}
 