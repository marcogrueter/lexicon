<?php namespace spec\Anomaly\Lexicon\View;

use Anomaly\Lexicon\Test\Spec;
use Illuminate\Container\Container;

/**
 * Class ViewSpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\View
 */
class ViewSpec extends Spec
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

    function it_can_render_the_contents_of_the_view()
    {
        $this->render()->shouldReturn('<h1>Hello Mr. Anderson!</h1>');
    }

}
