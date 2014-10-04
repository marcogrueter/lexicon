<?php namespace spec\Anomaly\Lexicon\Plugin;

use Anomaly\Lexicon\Test\Spec;

/**
 * Class StubPluginSpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Plugin
 */
class StubPluginSpec extends Spec
{

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Plugin\StubPlugin');
    }

    function it_can_get_foo()
    {
        $this->foo()->shouldReturn('FOO, BAR, BAZ!');
    }

    function it_can_filter()
    {
        $this->setAttributes(['value' => 'text']);
        $this->__call('md5', ['value' => 'text'])->shouldReturn('1cb251ec0d568de6a929b520c4aed8d1');
    }

    function it_can_parse()
    {
        $this->setAttributes(['value' => 'text']);
        $this->__call('uppercase', ['value' => 'text'])->shouldReturn('TEXT');
    }

}
