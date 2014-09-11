<?php
use Anomaly\Lexicon\Node\SectionAppend;
use Anomaly\Lexicon\Node\SectionOverwrite;

/**
 * Created by PhpStorm.
 * User: ob
 * Date: 9/11/14
 * Time: 6:00 AM
 */

class SectionOverwriteTest extends LexiconTestCase
{

    public function testCompilesExpectedSource()
    {
        $node = new SectionOverwrite($this->lexicon);

        $content = '{{ overwrite }}';

        $expected = "\$__data['__env']->stopSection(true);";

        $result = $this->compileNode($node, $parent = null, $content);

        $this->assertEquals($expected, $result);
    }
}
 