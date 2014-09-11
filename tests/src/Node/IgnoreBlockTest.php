<?php
use Anomaly\Lexicon\Node\IgnoreBlock;

/**
 * Created by PhpStorm.
 * User: ob
 * Date: 9/11/14
 * Time: 5:30 AM
 */

class IgnoreBlockTest extends LexiconTestCase
{

    public function testRendersTagWithoutParsingIt()
    {
        $node = new IgnoreBlock($this->lexicon);

        $content = '{{ ignore }}{{ tag }}{{ /ignore }}';

        $expected = '{{ tag }}';

        $result = $this->compileNode($node, $parent = null, $content);

        $this->assertEquals($expected, $result);
    }
}
 