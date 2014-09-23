<?php namespace spec\Anomaly\Lexicon\View;

use Anomaly\Lexicon\Lexicon;
use Anomaly\Lexicon\Stub\ArrayAccessObject;
use Anomaly\Lexicon\Stub\SimpleObject;
use Anomaly\Lexicon\Stub\StringObject;
use Anomaly\Lexicon\Stub\TraversableObject;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class FactorySpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\View
 */
class FactorySpec extends ObjectBehavior
{

    function let()
    {
        $this->beConstructedThrough('stub');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\View\Factory');
    }

    function it_can_make_view()
    {
        $this->make('test::view/hello')->shouldHaveType('Anomaly\Lexicon\Contract\View\ViewInterface');
    }

    function it_can_parse_and_make_view()
    {
        $this->parse('{{ hello }}')->shouldHaveType('Anomaly\Lexicon\Contract\View\ViewInterface');
    }

    function it_can_make_view_with_alias()
    {
        $this->alias('test::view/hello', 'foo');
        $this->make('foo')->shouldHaveType('Anomaly\Lexicon\Contract\View\ViewInterface');
    }

    function it_can_get_variable_from_plugin()
    {
        $this->variable([], 'stub.foo')->shouldReturn('FOO, BAR, BAZ!');
    }
    
    function it_returns_null_if_data_is_null()
    {
        $this->variable(null, '')->shouldReturn(null);
    }
    
    function it_can_get_array_size()
    {
        $data = [
            'list' => [
                'one',
                'two',
                'three'
            ]
        ];

        $this->variable($data, 'list.size')->shouldReturn(3);
    }

    function it_can_get_string_size()
    {
        $data = [
            'name' => 'Anomaly'
        ];

        $this->variable($data, 'name.size')->shouldReturn(7);
    }

    function it_returns_null_if_variable_in_array_does_not_exist()
    {
        $this->variable([], 'nonexistent')->shouldReturn(null);
    }
    
    function it_can_get_variable_from_ArrayAccess_object()
    {
        $data = [
            'thing' => new ArrayAccessObject(),
        ];

        $this->variable($data, 'thing.foo')->shouldReturn('value from array access object');
    }
    
    function it_returns_null_if_variable_in_ArrayAccess_object_does_not_exist()
    {
        $data = [
            'thing' => new ArrayAccessObject(),
        ];

        $this->variable($data, 'thing.nonexistent')->shouldReturn(null);
    }
    
    function it_can_get_variable_from_a_simple_object_method()
    {
        $data = [
            'simple' => new SimpleObject()
        ];

        $this->variable($data, 'simple.foo')->shouldReturn('value from method');
    }

    function it_returns_null_if_exception_happens_while_getting_variable_from_a_simple_object_method()
    {
        $data = [
            'simple' => new SimpleObject()
        ];

        $this->variable($data, 'simple.fragile')->shouldReturn(null);
    }

    function it_can_get_variable_from_a_simple_object_property()
    {
        $data = [
            'simple' => new SimpleObject()
        ];

        $this->variable($data, 'simple.bar')->shouldReturn('value from property');
    }

    function it_returns_null_if_simple_object_property_does_not_exist()
    {
        $data = [
            'simple' => new SimpleObject()
        ];

        $this->variable($data, 'simple.nonexistent')->shouldReturn(null);
    }
    
    function it_can_get_any_value()
    {
        $this->expected('whatever', Lexicon::EXPECTED_ANY)->shouldReturn('whatever');
    }
    
    function it_can_get_string_that_is_expected_to_be_echoed()
    {
        $this->expected('string', Lexicon::EXPECTED_ECHO)->shouldReturn('string');
    }

    function it_can_get_float_that_is_expected_to_be_echoed()
    {
        $this->expected(3.50, Lexicon::EXPECTED_ECHO)->shouldReturn(3.50);
    }

    function it_can_get_number_that_is_expected_to_be_echoed()
    {
        $this->expected(3, Lexicon::EXPECTED_ECHO)->shouldReturn(3);
    }

    function it_can_get_boolean_that_is_expected_to_be_echoed()
    {
        $this->expected(true, Lexicon::EXPECTED_ECHO)->shouldReturn(true);
    }

    function it_can_get_null_that_is_expected_to_be_echoed()
    {
        $this->expected(null, Lexicon::EXPECTED_ECHO)->shouldReturn(null);
    }

    function it_can_get_object_that_implements_toString_and_is_expected_to_be_echoed()
    {
        $stringObject = new StringObject();
        $this->expected($stringObject, Lexicon::EXPECTED_ECHO)->shouldReturn($stringObject);
    }

    function it_can_get_array_that_is_expected_to_be_traversable()
    {
        $this->expected(array(), Lexicon::EXPECTED_TRAVERSABLE)->shouldReturn(array());
    }

    function it_can_get_object_that_is_expected_to_be_traversable()
    {
        $traversableObject = new TraversableObject();
        $this->expected($traversableObject, Lexicon::EXPECTED_TRAVERSABLE)->shouldReturn($traversableObject);
    }

}
