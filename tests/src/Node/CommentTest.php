<?php namespace Anomaly\Lexicon\Test\Node;

use Anomaly\Lexicon\Node\Comment;
use Anomaly\Lexicon\Test\LexiconTestCase;

/**
 * Class SingleNullTest
 *
 * @package Anomaly\Lexicon\Test\Node
 */
class SingleNullTest extends LexiconTestCase
{

    /**
     * Comments should always compile to null regardless of any other factors
     */
    public function testCommentCompilesToNull()
    {
        $result = (new Comment($this->lexicon))->make([])->compile();

        $this->assertNull($result);
    }

    public function testRegexMatches()
    {
        $content = '{{-- This comment will not be rendered --}}';
    }
}
 