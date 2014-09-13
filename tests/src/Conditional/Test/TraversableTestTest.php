<?php namespace Anomaly\Lexicon\Test\Conditional\Test;

use Anomaly\Lexicon\Conditional\Test\TraversableTest;
use Anomaly\Lexicon\Test\LexiconTestCase;
use Anomaly\Lexicon\Test\View\ObjectStub;

/**
 * Class TraversableTestTest
 *
 * @package Anomaly\Lexicon\Test\Conditional\Test
 */
class TraversableTestTest extends LexiconTestCase
{
    /**
     * @var TraversableTest
     */
    protected $booleanTest;

    /**
     * Set up test
     */
    public function setUpTest()
    {
        $this->booleanTest = new TraversableTest();
    }

    /**
     * Test In boolean test
     */
    public function testInArray()
    {
        $data = [
            'one',
            'two',
            'three'
        ];

        $this->assertTrue($this->booleanTest->in('two', $data));
    }

    /**
     * Test In boolean test
     */
    public function testInIteratorAggregate()
    {
        $data = new TraversableObjectStub();

        $this->assertTrue($this->booleanTest->in('two', $data));
    }

    /**
     * Test in array access
     */
    public function testInArrayAccess()
    {
        $data = new ObjectStub();

        $this->assertTrue($this->booleanTest->in('bar', $data));
    }

    /**
     * Test in simple object
     */
    public function testInSimpleObject()
    {
        $data = new \stdClass();

        $data->yes = 'yes';

        $this->assertTrue($this->booleanTest->in('yes', $data));
    }

}
 