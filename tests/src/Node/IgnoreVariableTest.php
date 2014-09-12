<?php namespace Anomaly\Lexicon\Test\Node;

use Anomaly\Lexicon\Node\IgnoreVariable;
use Anomaly\Lexicon\Test\LexiconTestCase;

/**
 * Class IgnoreVariableTest
 *
 * @package Anomaly\Lexicon\Test\Node
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
 