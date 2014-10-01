<?php namespace spec\Anomaly\Lexicon\Node;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\Node\NodeInterface;
use Anomaly\Lexicon\Node\NodeCollection;
use Anomaly\Lexicon\Node\NodeExtractor;
use Anomaly\Lexicon\Node\NodeFinder;
use Anomaly\Lexicon\Node\Variable;
use Anomaly\Lexicon\Stub\Node\Node;
use Anomaly\Lexicon\Test\Spec;

/**
 * Class NodeFactorySpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Node
 */
class NodeFactorySpec extends Spec
{

    function let()
    {
        $this->beConstructedThrough('stub');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Node\NodeFactory');
    }

    function it_can_get_lexicon()
    {
        $this->getLexicon()->shouldImplement('Anomaly\Lexicon\Contract\LexiconInterface');
    }

    function it_can_set_and_get_a_node_group()
    {
        $this->setNodeGroup('variables')->getNodeGroup()->shouldReturn('variables');
    }

    function it_can_remove_a_node_type_from_a_node_group()
    {
        $this->getNodeTypes()->shouldHaveNodeCount(17);


        $this
            ->removeNodeTypeFromNodeGroup('Anomaly\Lexicon\Node\NodeType\Variable')
            ->getNodeTypes()->shouldHaveNodeCount(16);
    }

    function it_can_set_and_get_node_types()
    {
        $this->getNodeTypes()->shouldBeArray();
    }

    function it_can_register_a_single_node_group()
    {
        $this->registerNodeGroup([
                'Anomaly\Lexicon\Stub\Node\Node',
                'Anomaly\Lexicon\Stub\Node\Node2',
            ], 'custom_node_group1');

        $this->getNodeTypes('custom_node_group1')->shouldHaveNodeCount(2);
    }

    function it_can_register_multiple_node_groups()
    {
        $this->registerNodeGroups([
                'custom_node_group1' => [
                    'Anomaly\Lexicon\Stub\Node\Node',
                    'Anomaly\Lexicon\Stub\Node\Node2',
                    'Anomaly\Lexicon\Stub\Node\Node3',
                ],
                'custom_node_group2' => [
                    'Anomaly\Lexicon\Stub\Node\Node',
                ],
            ]);

        $this->getNodeTypes('custom_node_group1')->shouldHaveNodeCount(3);
    }

    function it_can_set_and_get_the_node_group()
    {
        $this->setNodeGroup('foo')->getNodeGroup()->shouldReturn('foo');
    }

    function it_can_set_and_get_attribute_node_types()
    {
        $this->setAttributeNodeTypes([
                'Anomaly\Lexicon\Stub\Node\Node',
                'Anomaly\Lexicon\Stub\Node\Node2',
                'Anomaly\Lexicon\Stub\Node\Node3',
            ])
            ->getAttributeNodeTypes()->shouldHaveNodeCount(3);
    }

    function it_can_get_root_node_type()
    {
        $this->registerNodeGroup([
                'Anomaly\Lexicon\Stub\Node\Node',
                'Anomaly\Lexicon\Stub\Node\Node2',
                'Anomaly\Lexicon\Stub\Node\Node3',
                'Anomaly\Lexicon\Stub\Node\Root',
            ], 'custom_node_group');

        $this
            ->getRootNodeType('custom_node_group')
            ->shouldImplement('Anomaly\Lexicon\Contract\Node\RootInterface');
    }

    function it_throws_root_node_type_not_found_exception()
    {
        $this->removeNodeTypeFromNodeGroup('Anomaly\Lexicon\Node\NodeType\Block');
        $this
            ->shouldThrow('Anomaly\Lexicon\Exception\RootNodeTypeNotFoundException')
            ->duringGetRootNodeType();
    }

    function it_can_get_node_collection()
    {
        $this->getCollection()->shouldHaveType('Anomaly\Lexicon\Node\NodeCollection');
    }

    function it_can_generate_a_random_id_string()
    {
        $this->generateId()->shouldBeString();
    }

    public function it_can_create_new_node_type_from_string()
    {
        $this->newNodeType('Anomaly\Lexicon\Stub\Node\Node')->shouldImplement(
            'Anomaly\Lexicon\Contract\Node\NodeInterface'
        );
    }

    function it_can_add_node(Node $node)
    {
        $this->addNode($node);
    }

    function it_can_make_node_of_type(Node $nodeType, NodeFinder $nodeFinder)
    {
        $nodeType->setId($id = 'F4pwOfe4eAaTJxFf483ZsQnFL3ALqXjl')->shouldBeCalled();
        $nodeType->incrementDepth()->willReturn(true);
        $nodeType->setParentId(null)->shouldBeCalled();
        $nodeType->setMatch([])->shouldBeCalled();
        $nodeType->setOffset(0)->shouldBeCalled();
        $nodeType->setDepth(1)->shouldBeCalled();
        $nodeType->setup()->shouldBeCalled();
        $nodeType->getItemAliasFromRawAttributes()->shouldBeCalled()->willReturn('book');
        $nodeType->setItemAlias('book')->shouldBeCalled();

        $this->make(
            $nodeType,
            $match = [],
            $parent = null,
            $offset = 0,
            $depth = 0,
            $id
        )->shouldHaveType('Anomaly\Lexicon\Stub\Node\Node');
    }

    function it_can_get_node_by_id(Node $node)
    {
        $node->getId()->willReturn('stub-id-1');
        $this->addNode($node);
        $this->getById('stub-id-1')->shouldHaveType('Anomaly\Lexicon\Stub\Node\Node');
    }

    function it_can_get_multiple_nodes_by_id(Node $node, Node $node2)
    {
        $node->getId()->willReturn('stub-id-1');
        $node2->getId()->willReturn('stub-id-2');
        $this->addNode($node);
        $this->addNode($node2);
        $this->getByIds(['stub-id-1', 'stub-id-2'])->shouldHaveNodeCount(2);
    }

    function it_can_add_add_node_group_path()
    {
        $this->addNodeGroupPath('path_foo', 'node_group_1');
    }

    function it_can_get_node_group_from_path()
    {
        $this
            ->addNodeGroupPath('path_foo', 'node_group_1')
            ->getNodeGroupFromPath('path_foo')
            ->shouldReturn('node_group_1');
    }

    function it_can_create_child_nodes()
    {
        $node = $this->getRootNode('{{ var1 }}{{ var2 }}');
        $this->createChildNodes($node);
    }
    
    function it_can_get_node_extractor()
    {
        $this->getNodeExtractor()->shouldHaveType('Anomaly\Lexicon\Node\NodeExtractor');
    }
    
    function it_can_extract_node_from_parent(NodeInterface $child, NodeInterface $parent)
    {
        $this->extract($child, $parent);
    }
    
    function it_can_inject_node_into_parent(NodeInterface $child, NodeInterface $parent)
    {
        $this->inject($child, $parent);
    }

    function it_can_get_root_node()
    {
        $this->getRootNode('content')->shouldImplement('Anomaly\Lexicon\Contract\Node\RootInterface');
    }

}
