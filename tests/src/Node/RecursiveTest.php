<?php namespace Anomaly\Lexicon\Test\Node;

use Anomaly\Lexicon\Node\Block;
use Anomaly\Lexicon\Node\Recursive;
use Anomaly\Lexicon\Test\LexiconTestCase;

/**
 * Class RecursiveTest
 *
 * @package Anomaly\Lexicon\Test\Node
 */
class RecursiveTest extends LexiconTestCase
{

    /**
     * Set up node
     */
    public function setUpNode()
    {
        $this->node = new Recursive($this->lexicon);
    }

    /**
     * Test compiles expected source
     */
    public function testCompilesExpectedSource()
    {
        $template =
            '<ul>
            {{ nav }}
            <li>
                {{ title }}
                {{ if children }}
                <ul>
                    {{ recursive }}
                </ul>
                {{ endif }}
            </li>
            {{ /nav }}
        </ul>';

        $parent = $this->parseAndMakeNode($this->makeBlockNode(), $parent = null, $template);

        $expected = "echo \$__data['__env']->parse('{{ nav }}
            <li>
                {{ title }}
                {{ if children }}
                <ul>
                    {{ recursive }}
                </ul>
                {{ endif }}
            </li>
            {{ /nav }}',\$__data);";

        $result = $this->compileNode($this->node, $parent, $template);

        $this->assertEquals($expected, $result);
    }
}
 