<?php namespace spec\Anomaly\Lexicon\Plugin;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Plugin\CounterPlugin;
use Anomaly\Lexicon\Plugin\StubPlugin;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class PluginHandlerSpec
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Plugin
 */
class PluginHandlerSpec extends ObjectBehavior
{

    function let(LexiconInterface $lexicon)
    {
        $this->setLexicon($lexicon);
    }
    
    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Plugin\PluginHandler');
    }

    function it_can_set_lexicon(LexiconInterface $lexicon)
    {
        $this->setLexicon($lexicon);
    }

    function it_can_register_multiple_plugins()
    {
        $this->registerPlugins([
                'stub' => 'Anomaly\Lexicon\Plugin\StubPlugin',
                'stub2' => 'Anomaly\Lexicon\Plugin\StubPlugin',
                'stub3' => 'Anomaly\Lexicon\Plugin\StubPlugin',
            ]);
    }

    function it_can_get_plugin_by_name(LexiconInterface $lexicon)
    {
        $lexicon->getScopeGlue()->willReturn('.');
        $this->registerPlugin('stub', 'Anomaly\Lexicon\Plugin\StubPlugin');
        $this->get('stub.foo')->shouldHaveType('Anomaly\Lexicon\Plugin\StubPlugin');
    }
    
    function it_can_call_plugin_method_with_attributes(StubPlugin $plugin)
    {
        $plugin->setAttributes([])->shouldBeCalled();
        $plugin->setContent('')->shouldBeCalled();
        $plugin->foo()->willReturn('FOO, BAR, BAZ!');
        $this->call($plugin, 'foo', [], '')->shouldReturn('FOO, BAR, BAZ!');
    }

    function it_can_assert_if_a_plugin_call_should_be_filtered(LexiconInterface $lexicon)
    {
        $lexicon->getScopeGlue()->willReturn('.');
        $this->registerPlugin('stub', 'Anomaly\Lexicon\Plugin\StubPlugin');
        $this->isFilter('stub.foo')->shouldBe(false);
        $this->isFilter('stub.md5')->shouldBe(true);
        $this->isFilter('stub.uppercase')->shouldBe(false);
    }

    function it_can_assert_if_a_plugin_call_should_be_parsed(LexiconInterface $lexicon)
    {
        $lexicon->getScopeGlue()->willReturn('.');
        $this->registerPlugin('stub', 'Anomaly\Lexicon\Plugin\StubPlugin');
        $this->isParse('stub.foo')->shouldBe(false);
        $this->isParse('stub.md5')->shouldBe(false);
        $this->isParse('stub.uppercase')->shouldBe(true);
    }

}
