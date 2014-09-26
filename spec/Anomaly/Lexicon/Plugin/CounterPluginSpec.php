<?php namespace spec\Anomaly\Lexicon\Plugin;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class CounterPluginSpec
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Plugin
 */
class CounterPluginSpec extends ObjectBehavior
{

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Plugin\CounterPlugin');
    }

    function it_can_count()
    {
        $this->count()->shouldReturn(1);
        $this->count()->shouldReturn(2);
        $this->count()->shouldReturn(3);
        $this->count()->shouldReturn(4);
        $this->count()->shouldReturn(5);
    }
    
    function it_can_show_count()
    {
        $this->count();
        $this->count();
        $this->count();
        $this->show()->shouldReturn(3);
    }
    
    function it_returns_null_if_return_is_set_to_false()
    {
        $this->setAttributes(['return' => false]);
        $this->count()->shouldReturn(null);
    }
}
