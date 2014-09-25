<?php namespace spec\Anomaly\Lexicon;

use Anomaly\Lexicon\Conditional\ConditionalHandler;
use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\Support\Container;
use Anomaly\Lexicon\Plugin\PluginHandler;
use Anomaly\Lexicon\Stub\Node\Node;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Session\SessionInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class LexiconSpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon
 */
class LexiconSpec extends ObjectBehavior
{

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Contract\LexiconInterface');
    }

    function it_can_set_debug_mode()
    {
        $this->setDebug(true);
    }
    
    function it_can_check_if_debug_mode_is_enabled()
    {
        $this->isDebug()->shouldBeBoolean();
    }

    function it_can_set_the_scope_glue()
    {
        $this->setScopeGlue(':');
    }

    function it_can_get_scope_glue()
    {
        $this->getScopeGlue()->shouldBeString();
    }

    function it_has_the_conditional_handler()
    {
        $this->getConditionalHandler()->shouldImplement(
            'Anomaly\Lexicon\Contract\Conditional\ConditionalHandlerInterface'
        );
    }

    function it_has_the_plugin_handler()
    {
        $this->getPluginHandler()->shouldImplement('Anomaly\Lexicon\Contract\Plugin\PluginHandlerInterface');
    }

    function it_can_register_a_single_plugin()
    {
        $plugin = 'Anomaly\Lexicon\Stub\Plugin\StubPlugin';
        $this->registerPlugin('stub', $plugin);
        $this->getPluginHandler()->get('stub.foo')->shouldHaveType($plugin);
    }
    
    function it_can_register_multiple_plugins(PluginHandler $pluginHandler)
    {
        $this->registerPlugins([
                'foo' => 'FooPlugin',
                'bar' => 'BarPlugin'
            ]);
    }

    function it_can_add_parse_path()
    {
        $this->addParsePath('{{ foo }}'); // the template is equivalent to the path
        $this->getParsePaths()->shouldHaveKey('{{ foo }}');
    }

    function it_can_check_if_path_is_parse_able()
    {
        $this->addParsePath('{{ bar }}');
        $this->isParsePath('{{ bar }}')->shouldBe(true);
    }

    function it_can_get_view_template_path()
    {
        $this->getCompiledViewTemplatePath()->shouldBeString();
    }

    function it_can_allow_php()
    {
        $this->setAllowPhp(true);
    }

    function it_can_check_if_php_is_allowed()
    {
        $this->isPhpAllowed()->shouldBeBoolean();
    }

    function it_can_set_and_get_the_view_namespace()
    {
        $this
            ->setCompiledViewNamespace('Foo')
            ->getCompiledViewNamespace()
            ->shouldReturn('Foo');
    }

    function it_can_set_and_get_the_view_class_prefix()
    {
        $this
            ->setCompiledViewClassPrefix('View_')
            ->getCompiledViewClassPrefix()
            ->shouldReturn('View_');
    }
    
    function it_can_get_the_view_class()
    {
        $this
            ->setCompiledViewClassPrefix('View_')
            ->getCompiledViewClass('foo')
            ->shouldReturn('View_foo');
    }
    
    function it_can_get_the_full_view_class()
    {
        $this
            ->setCompiledViewNamespace('Foo')
            ->setCompiledViewClassPrefix('View_')
            ->getCompiledViewFullClass('bar')
            ->shouldReturn('Foo\View_bar');
    }

    function it_can_get_root_alias()
    {
        $this->getRootAlias()->shouldBeString();
    }
    
    function it_can_set_extension()
    {
        $this
            ->setExtension('html')
            ->getExtension()
            ->shouldReturn('html');
    }
    
    function it_can_set_storage_path()
    {
        $this
            ->setStoragePath('storage/views')
            ->getStoragePath()
            ->shouldReturn('storage/views');
    }
    
    function it_can_set_view_paths()
    {
        $this
            ->setViewPaths(['views'])
            ->getViewPaths()
            ->shouldReturn(['views']);
    }
}
