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
        $node = new Comment($this->lexicon);

        $default = 'NOT_NULL';

        $content = '{{-- This comment will not be rendered --}}';

        $result = $this->compileNode($node, $parent = null, $content, $default);

        $this->assertNull($result);
    }
}
 