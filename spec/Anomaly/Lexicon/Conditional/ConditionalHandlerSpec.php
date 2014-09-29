<?php namespace spec\Anomaly\Lexicon\Conditional;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class ConditionalHandlerSpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Conditional
 */
class ConditionalHandlerSpec extends ObjectBehavior
{

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Conditional\ConditionalHandler');
    }

    function it_can_register_and_get_boolean_test_types()
    {
        $this->registerBooleanTestTypes(
            [
                'stringTest'      => 'Anomaly\Lexicon\Conditional\Test\StringTest',
                'itemTest' => 'Anomaly\Lexicon\Conditional\Test\ItemTest'
            ]
        )->getTestTypes()->shouldHaveCount(2);
    }

    function it_can_get_test_operators()
    {
        $this->registerBooleanTestTypes(
            [
                'stringTest'      => 'Anomaly\Lexicon\Conditional\Test\StringTest',
                'itemTest' => 'Anomaly\Lexicon\Conditional\Test\ItemTest'
            ]
        )->getTestOperators()->shouldBeArray();
    }

    function it_can_run_equals_exactly_boolean_tests()
    {
        $this->booleanTest('foo', 'bar', '===')->shouldBe(false);
    }

    function it_can_run_not_equals_exactly_boolean_test()
    {
        $this->booleanTest('foo', 'bar', '!==')->shouldBe(true);
    }

    function it_can_run_equals_boolean_tests()
    {
        $this->booleanTest('foo', 'yang', '==')->shouldBe(false);
    }

    function it_can_run_not_equals_boolean_test()
    {
        $this->booleanTest('foo', 'bar', '!=')->shouldBe(true);
    }

    function it_can_run_is_more_than_or_equals_boolean_tests()
    {
        $this->booleanTest(508888, 10000, '>=')->shouldBe(true);
    }

    function it_can_run_is_less_than_or_equals_boolean_tests()
    {
        $this->booleanTest(5, 5000000000, '<=')->shouldBe(true);
    }

    function it_can_run_is_more_than_boolean_tests()
    {
        $this->booleanTest(1000000000, 90, '>')->shouldBe(true);
    }

    function it_can_run_is_less_than_boolean_tests()
    {
        $this->booleanTest(14084239, 2076, '<')->shouldBe(false);
    }

    function it_can_run_custom_boolean_tests()
    {
        $this->registerBooleanTestTypes(
            [
                'stringTest' => 'Anomaly\Lexicon\Conditional\Test\StringTest',
            ]
        );

        $this->booleanTest('funkadelic', 'funk', 'contains')->shouldBe(true);
    }

}
