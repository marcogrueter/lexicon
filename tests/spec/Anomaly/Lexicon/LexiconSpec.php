<?php namespace spec\Anomaly\Lexicon;

use Anomaly\Lexicon\Conditional\ConditionalHandler;
use Anomaly\Lexicon\Contract\Conditional\ConditionalHandlerInterface;
use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\Plugin\PluginHandlerInterface;
use Anomaly\Lexicon\Foundation;
use Anomaly\Lexicon\Node\NodeFactory;
use Anomaly\Lexicon\Plugin\PluginHandler;
use Anomaly\Lexicon\Stub\Node\Node;
use Anomaly\Lexicon\Support\Container;
use Anomaly\Lexicon\Test\Spec;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Session\SessionInterface;

/**
 * Class LexiconSpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon
 */
class LexiconSpec extends Spec
{

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Contract\LexiconInterface');
    }

    function it_can_register_dependencies()
    {
        $this->register();
    }

    function it_can_set_and_get_the_foundation(Foundation $foundation)
    {
        $this->setFoundation($foundation)->getFoundation()->shouldHaveType('Anomaly\Lexicon\Foundation');
    }

    function it_can_set_and_get_debug_mode()
    {
        $this->setDebug(true)->isDebug()->shouldBe(true);
    }

    function it_can_set_and_get_the_scope_glue()
    {
        $this->setScopeGlue(':')->getScopeGlue()->shouldBe(':');
    }

    function it_can_set_and_get_the_conditional_handler(ConditionalHandlerInterface $conditionalHandler)
    {
        $this
            ->setConditionalHandler($conditionalHandler)
            ->getConditionalHandler()
            ->shouldImplement('Anomaly\Lexicon\Contract\Conditional\ConditionalHandlerInterface');
    }

    function it_can_set_and_get_the_plugin_handler(PluginHandlerInterface $pluginHandler)
    {
        $this
            ->setPluginHandler($pluginHandler)
            ->getPluginHandler()
            ->shouldImplement('Anomaly\Lexicon\Contract\Plugin\PluginHandlerInterface');
    }

    function it_can_register_a_single_plugin()
    {
        $this->registerPlugin('stub', 'Anomaly\Lexicon\Plugin\StubPlugin');
    }
    
    function it_can_register_multiple_plugins()
    {
        $this->registerPlugins([
                'foo' => 'FooPlugin',
                'bar' => 'BarPlugin'
            ]);
    }

    function it_can_get_plugins()
    {
        $this->getPlugins()->shouldBeArray();
    }

    function it_can_register_a_single_boolean_test_type()
    {
        $this->registerBooleanTestType('stringTest', 'Anomaly\Lexicon\Conditional\Test\StringTest');
    }

    function it_can_register_multiple_boolean_test_types()
    {
        $this->registerBooleanTestTypes([
                'stringTest' => 'Anomaly\Lexicon\Conditional\Test\StringTest',
                'itemTest' => 'Anomaly\Lexicon\Conditional\Test\ItemTest'
            ]);
    }
    
    function it_can_get_boolean_test_types()
    {
        $this->getBooleanTestTypes()->shouldBeArray();
    }
    
    function it_can_add_parse_path()
    {
        $this->addStringTemplate('{{ foo }}');
        $this->getStringTemplates()->shouldHaveKey('{{ foo }}');
    }

    function it_can_check_if_path_is_string_template()
    {
        $this->addStringTemplate('{{ bar }}');
        $this->isStringTemplate('{{ bar }}')->shouldBe(true);
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

    function it_can_register_a_node_type(NodeFactory $nodeFactory)
    {
        $this->registerNodeType('Anomaly\Lexicon\Stub\Node');
    }

    function it_can_register_single_node_group()
    {
        $this->registerNodeGroup([
                'Anomaly\Lexicon\Stub\Node',
                'Anomaly\Lexicon\Stub\Node2'.
                'Anomaly\Lexicon\Stub\Node3'
            ], 'custom_node_group');
    }

    function it_can_register_multiple_node_groups()
    {
        $this->registerNodeGroups([
                'custom_node_group' => [
                    'Anomaly\Lexicon\Stub\Node',
                    'Anomaly\Lexicon\Stub\Node2'.
                    'Anomaly\Lexicon\Stub\Node3'
                ],
                'custom_node_group2' => [
                    'Anomaly\Lexicon\Stub\Node2'.
                    'Anomaly\Lexicon\Stub\Node3'
                ],
            ]);
    }

    function it_can_get_node_groups()
    {
        $this->getNodeGroups()->shouldBeArray();
    }
    
    function it_can_set_and_get_config()
    {
        $this
            ->setConfigPath(__DIR__ . '/../../../src/config')
            ->getConfigPath()
            ->shouldReturn(__DIR__ . '/../../../src/config');
    }
    
    function it_can_get_container()
    {
        $this->getContainer()->shouldHaveType('Illuminate\Container\Container');
    }
    
    function it_can_set_and_get_node_factory(NodeFactory $nodeFactory)
    {
        $this->setNodeFactory($nodeFactory)->getNodeFactory()->shouldHaveType('Anomaly\Lexicon\Node\NodeFactory');
    }

    function it_can_add_and_get_view_namespaces()
    {
        $this->addNamespace('foo', 'some/directory')->getNamespaces()->shouldHaveCount(1);
    }
    
    function it_can_set_and_get_magic_method_objects()
    {
        $this->addMagicMethodClasses(['Foo']);
        $this->addMagicMethodClass('Bar');
        $this->getMagicMethodClasses()->shouldReturn([
                'Illuminate\Database\Eloquent\Relations\Relation',
                'Foo',
                'Bar'
            ]);
    }

}
