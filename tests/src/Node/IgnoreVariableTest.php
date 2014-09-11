<?php
use Anomaly\Lexicon\Node\IgnoreVariable;

/**
 * Created by PhpStorm.
 * User: ob
 * Date: 9/11/14
 * Time: 5:51 AM
 */

class IgnoreVariableTest extends LexiconTestCase
{

    public function testRendersTagWithoutParsingIt()
    {
        $node = new IgnoreVariable($this->lexicon);

        $content = '@{{ unprocessed }}';

        $expected = '{{ unprocessed }}';

        $result = $this->compileNode($node, $parent = null, $content);

        $this->assertEquals($expected, $result);
    }
}
 