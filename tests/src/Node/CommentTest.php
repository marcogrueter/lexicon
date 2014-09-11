<?php
use Anomaly\Lexicon\Node\Comment;

/**
 * Created by PhpStorm.
 * User: ob
 * Date: 9/11/14
 * Time: 5:43 AM
 */

class SingleNullTest extends LexiconTestCase
{

    public function testCommentSingleNullTypeCompilesToNull()
    {
        $comment = new Comment($this->lexicon);

        $result = 'NOT_NULL';

        foreach($comment->getMatches('{{-- This comment won\'t render. --}}') as $match) {
            $result = $comment->make($match)->compile();
            break;
        }

        $this->assertNull($result);
    }
}
 