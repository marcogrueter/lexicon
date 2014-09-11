<?php
use Anomaly\Lexicon\Node\SectionAppend;

/**
 * Created by PhpStorm.
 * User: ob
 * Date: 9/11/14
 * Time: 6:00 AM
 */

class SectionAppendTest extends LexiconTestCase
{

    public function testCompilesExpectedSource()
    {
        $node = new SectionAppend($this->lexicon);

        $content = '{{ append }}';

        $expected = "\$__data['__env']->appendSection();";

        $result = $this->compileNode($node, $parent = null, $content);

        $this->assertEquals($expected, $result);
    }
}
 