<?php namespace spec\Anomaly\Lexicon;

use Anomaly\Lexicon\Conditional\ConditionalHandler;
use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Plugin\PluginHandler;
use Anomaly\Lexicon\Stub\Node\Node;
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

    function let(ConditionalHandler $conditionalHandler, PluginHandler $pluginHandler)
    {
        $this->beConstructedWith($conditionalHandler, $pluginHandler);
    }

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
        $this->getConditionalHandler()->shouldHaveType(
            'Anomaly\Lexicon\Contract\Conditional\ConditionalHandlerInterface'
        );
    }

    function it_has_the_plugin_handler()
    {
        $this->getPluginhandler()->shouldImplement('Anomaly\Lexicon\Contract\Plugin\PluginHandlerInterface');
    }

    function it_can_get_array_of_node_types()
    {
        $this->registerNodeSet(['Anomaly\Lexicon\Stub\Node\Node']);
        $this->getNodeTypes()->shouldBeArray();
    }

    function it_can_get_array_of_attribute_node_types()
    {
        $this->getAttributeNodeTypes()->shouldBeArray();
    }

    public function it_can_create_new_node_type_from_string()
    {
        $this->newNodeType('Anomaly\Lexicon\Stub\Node\Node')->shouldImplement(
            'Anomaly\Lexicon\Contract\Node\NodeInterface'
        );
    }
    
    function it_can_remove_a_note_type_from_a_node_set()
    {
        $this
            ->registerNodeSet(['Anomaly\Lexicon\Stub\Node\Node', 'Anomaly\Lexicon\Stub\Node\Node2'])
            ->removeNodeTypeFromNodeSet('Anomaly\Lexicon\Stub\Node\Node');

        $this->getNodeTypes()->shouldHaveCount(1);
    }

    function it_can_get_node_set()
    {
        $this->getNodeSet()->shouldBeArray();
    }

    function it_can_register_a_single_node_set()
    {
        $this->registerNodeSet([
                'Anomaly\Lexicon\Stub\Node\Node',
                'Anomaly\Lexicon\Stub\Node\Node2',
            ], 'custom_node_set');

        $this->getNodeTypes('custom_node_set')->shouldHaveCount(2);
    }

    function it_can_register_multiple_node_sets()
    {
        $this->registerNodeSets([
               'custom_node_set' => [
                   'Anomaly\Lexicon\Stub\Node\Node',
                   'Anomaly\Lexicon\Stub\Node\Node2',
                   'Anomaly\Lexicon\Stub\Node\Node3',
               ],
            ]);

        $this->getNodeTypes('custom_node_set')->shouldHaveCount(3);
    }

    function it_can_register_a_single_plugin(PluginHandler $pluginHandler)
    {
        $this->registerPlugin('foo', 'FooPlugin');
        $pluginHandler->register('foo', 'FooPlugin')->shouldBeCalled();
    }
    
    function it_can_register_multiple_plugins(PluginHandler $pluginHandler)
    {
        $this->registerPlugins([
                'foo' => 'FooPlugin',
                'bar' => 'BarPlugin'
            ]);

        $pluginHandler->register('foo', 'FooPlugin')->shouldBeCalled();
        $pluginHandler->register('bar', 'BarPlugin')->shouldBeCalled();
    }

    function it_can_get_root_node_type()
    {
        $this->registerNodeSet([
                'Anomaly\Lexicon\Stub\Node\Node',
                'Anomaly\Lexicon\Stub\Node\Node2',
                'Anomaly\Lexicon\Stub\Node\Node3',
                'Anomaly\Lexicon\Stub\Node\Root',
            ], 'custom_node_set');

        $this
            ->getRootNodeType('custom_node_set')
            ->shouldImplement('Anomaly\Lexicon\Contract\Node\RootInterface');
    }
    
    function it_throws_root_node_type_not_found_exception()
    {
        $this
            ->shouldThrow('Anomaly\Lexicon\Exception\RootNodeTypeNotFoundException')
            ->duringGetRootNodeType();
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

    function it_can_add_node(Node $node)
    {
        $this->addNode($node);
    }

    function it_can_get_node_by_id()
    {
        $this->addNode(new Node($this));
        $this->getNodeById('stub-id')->shouldImplement('Anomaly\Lexicon\Contract\Node\NodeInterface');
    }

    function it_can_set_and_get_view_template_path()
    {
        $this->setViewTemplatePath('test')->getViewTemplatePath()->shouldReturn('test');
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
            ->setViewNamespace('Foo')
            ->getViewNamespace()
            ->shouldReturn('Foo');
    }

    function it_can_set_and_get_the_view_class_prefix()
    {
        $this
            ->setViewClassPrefix('View_')
            ->getViewClassPrefix()
            ->shouldReturn('View_');
    }
    
    function it_can_get_the_view_class()
    {
        $this
            ->setViewClassPrefix('View_')
            ->getViewClass('foo')
            ->shouldReturn('View_foo');
    }
    
    function it_can_get_the_full_view_class()
    {
        $this
            ->setViewNamespace('Foo')
            ->setViewClassPrefix('View_')
            ->getFullViewClass('bar')
            ->shouldReturn('Foo\View_bar');
    }
    
    function it_can_add_add_node_set_path()
    {
        $this->addNodeSetPath('path_foo', 'nodeset_bar');
    }

    function it_can_get_node_set_from_path()
    {
        $this
            ->addNodeSetPath('path_foo', 'nodeset_bar')
            ->getNodeSetFromPath('path_foo')
            ->shouldReturn('nodeset_bar');
    }

}
