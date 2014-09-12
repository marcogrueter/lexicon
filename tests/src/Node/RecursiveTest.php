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

    public function testCompilesExpectedSource()
    {
        $block = new Block($this->lexicon);

        $recursive = new Recursive($this->lexicon);

        $content =
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

        $parent = $this->parseAndMakeNode($block, $parent = null, $content);

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

        $result = $this->compileNode($recursive, $parent, $content);

        $this->assertEquals($expected, $result);
    }
}
 