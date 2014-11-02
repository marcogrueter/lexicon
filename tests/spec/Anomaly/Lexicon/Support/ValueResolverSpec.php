<?php namespace spec\Anomaly\Lexicon\Support;

use Anomaly\Lexicon\Test\Spec;

/**
 * Class ValueResolverSpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Support
 */
class ValueResolverSpec extends Spec
{

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Support\ValueResolver');
    }

    function it_returns_string_null_if_passed_is_array_or_null_or_object()
    {
        $this->resolve(null)->shouldReturn('null');
        $this->resolve(array())->shouldReturn('null');
        $this->resolve(new \stdClass())->shouldReturn('null');
    }

    function it_returns_string_value_as_is_if_its_in_pass_array()
    {
        $this->resolve('null')->shouldReturn('null');
        $this->resolve('true')->shouldReturn('true');
        $this->resolve('false')->shouldReturn('false');
    }

    function it_can_return_string_value_including_single_quotes()
    {
        $this->resolve("'value'")->shouldReturn("'value'");
    }

    function it_can_return_string_value_without_double_quotes()
    {
        $this->resolve('"value"')->shouldReturn('value');
    }

    function it_can_return_string_digit_as_is()
    {
        $this->resolve('100')->shouldReturn('100');
    }

    function it_can_return_string_float_as_is()
    {
        $this->resolve('3.75')->shouldReturn('3.75');
    }
}
