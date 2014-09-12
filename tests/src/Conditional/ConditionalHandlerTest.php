<?php namespace Anomaly\Lexicon\Test\Conditional;

use Anomaly\Lexicon\Test\LexiconTestCase;

/**
 * Class ConditionalHandlerTest
 *
 * @package Anomaly\Lexicon\Test\Conditional
 */
class ConditionalHandlerTest extends LexiconTestCase
{

    /**
     * Test all boolean operators from the conditional handler
     */
    public function testCompare()
    {
        $this->assertTrue($this->conditionalHandler->compare(11, 11, '==='));
        $this->assertTrue($this->conditionalHandler->compare(0, false, '!=='));
        $this->assertTrue($this->conditionalHandler->compare(1, true, '=='));
        $this->assertTrue($this->conditionalHandler->compare(55, 66, '!='));
        $this->assertTrue($this->conditionalHandler->compare(88, 77, '>='));
        $this->assertTrue($this->conditionalHandler->compare(99, 99, '<='));
        $this->assertTrue($this->conditionalHandler->compare(22, 11, '>'));
        $this->assertTrue($this->conditionalHandler->compare(33, 44, '<'));
    }

    /**
     * Test custom boolean test in test type class
     */
    public function testCustomBooleanTest()
    {
        $this->conditionalHandler->registerBooleanTestTypes(
            [
                'custom' => 'Anomaly\Lexicon\Test\Conditional\CustomBooleanTestType'
            ]
        );

        // The custom `isCoolerThan` test always returns true regardless of the compared values
        // We just want to test that the mechanism works
        $this->assertTrue($this->conditionalHandler->compare('Lexicon', 'Lex', 'isCoolerThan'));
    }
}
 