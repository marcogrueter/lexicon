<?php namespace Anomaly\Lexicon\Test\Node;

use Anomaly\Lexicon\Node\SectionOverwrite;
use Anomaly\Lexicon\Test\LexiconTestCase;

/**
 * Class SectionOverwriteTest
 *
 * @package Anomaly\Lexicon\Test\Node
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
 