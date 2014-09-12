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

    /**
     * Set up node
     */
    public function setUpNode()
    {
        $this->node = new IgnoreVariable($this->lexicon);
    }

    /**
     * Test renders raw tag without parsing it
     */
    public function testRendersTagWithoutParsingIt()
    {
        $template = '@{{ unprocessed }}';

        $expected = '{{ unprocessed }}';

        $result = $this->compileNode($this->node, $parent = null, $template);

        $this->assertEquals($expected, $result);
    }

}
 