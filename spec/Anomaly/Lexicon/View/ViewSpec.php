<?php namespace spec\Anomaly\Lexicon\View;


use Illuminate\Container\Container;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class ViewSpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\View
 */
class ViewSpec extends ObjectBehavior
{

    function let()
    {
        $this->beConstructedThrough('stub', []);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\View\View');
    }

    function it_can_use_node_set()
    {
        $this->using('custom_node_set');
    }
    
    function it_can_use_node_set_by_calling_a_magic_method()
    {
        $this->usingCustomNodeSet();
    }
    
    function it_can_call_any_other_magic_method()
    {
        $this->withFoo('bar');
    }

}
