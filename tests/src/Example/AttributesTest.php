<?php namespace Anomaly\Lexicon\Test\Example;


use Anomaly\Lexicon\Lexicon;
use Anomaly\Lexicon\Test\LexiconTestCase;

/**
 * Class AttributesTest
 *
 * @package Anomaly\Lexicon\Test\Example
 */
class AttributesTest extends LexiconTestCase
{

    public function testNamedAttributes()
    {
        $result = $this->compiler->compileString('{{ object foo="FOO" bar="BAR" }}');

        $this->assertEquals('', $result);
    }

}
 