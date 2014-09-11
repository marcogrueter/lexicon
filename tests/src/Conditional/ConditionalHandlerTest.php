<?php

/**
 * Created by PhpStorm.
 * User: ob
 * Date: 9/11/14
 * Time: 5:09 AM
 */
class ConditionalHandlerTest extends LexiconTestCase
{


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

    public function testCustomBooleanTest()
    {
        $this->conditionalHandler->registerBooleanTestTypes(
            [
                'custom' => 'CustomBooleanTestType'
            ]
        );

        $this->assertTrue($this->conditionalHandler->compare('Lexicon', 'Lex', 'isCoolerThan'));
    }
}

class CustomBooleanTestType
{
    public function isCoolerThan($left, $right)
    {
        return true;
    }
}
 