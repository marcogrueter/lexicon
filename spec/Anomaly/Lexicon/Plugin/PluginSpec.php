<?php namespace spec\Anomaly\Lexicon\Plugin;

use Anomaly\Lexicon\Plugin\StubPlugin;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class PluginSpec
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Plugin
 */
class PluginSpec extends ObjectBehavior
{

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Plugin\Plugin');
    }

    function it_can_set_and_get_plugin_name()
    {
        $this->setPluginName('foo')->getPluginName()->shouldReturn('foo');
    }
    
    function it_can_set_and_get_named_attributes()
    {
        $this->setAttributes(['foo' => 'bar', 'ying' => 'yang']);
        $this->getAttribute('ying')->shouldReturn('yang');
    }
    
    function it_can_set_and_get_ordered_attributes_by_offset()
    {
        $this->setAttributes(['foo', 'bar', 'baz']);
        $this->getAttribute('', 1)->shouldReturn('bar');
    }
    
    function it_gets_default_value_if_attribute_is_not_found()
    {
        $this->getAttribute('foo', 0,'default')->shouldReturn('default');
    }
    
    function it_can_set_and_get_content()
    {
        $this->setContent('Hello')->getContent()->shouldReturn('Hello');
    }
    
    function it_can_call_magic_methods()
    {
        $this->__call('foo', [])->shouldReturn(null);
        $this->__call('md5', [])->shouldReturn(null);
        $this->__call('uppercase', [])->shouldReturn(null);
    }


}
