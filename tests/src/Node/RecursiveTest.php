<?php
use Anomaly\Lexicon\Node\Block;
use Anomaly\Lexicon\Node\Recursive;

/**
 * Created by PhpStorm.
 * User: ob
 * Date: 9/11/14
 * Time: 6:00 AM
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

        $parent = $this->makeNode($block, $parent = null, $content);

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
 