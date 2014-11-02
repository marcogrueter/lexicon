<?php namespace spec\Anomaly\Lexicon\Conditional\Test;

use Anomaly\Lexicon\Stub\ArrayAccessObject;
use Anomaly\Lexicon\Stub\SimpleObject;
use Anomaly\Lexicon\Test\Spec;

/**
 * Class ItemTestSpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Conditional\Test
 */
class ItemTestSpec extends Spec
{

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Conditional\Test\ItemTest');
    }

    function it_can_test_if_value_is_in_array()
    {
        $this->in('two', ['one', 'two', 'three'])->shouldBe(true);
    }

    function it_can_test_if_array_has_property()
    {
        $data = ['foo' => 'bar'];
        $this->has($data, 'foo')->shouldBe(true);
    }

    function it_can_test_if_array_access_object_has_property(ArrayAccessObject $object)
    {
        $object->offsetExists('foo')->willReturn(true);
        $this->has($object, 'foo')->shouldBe(true);
    }

    function it_can_test_if_simple_access_object_has_property_or_method(SimpleObject $object)
    {
        $this->has($object, 'bar')->shouldBe(true);
    }

}
