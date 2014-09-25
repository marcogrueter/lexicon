<?php namespace spec\Anomaly\Lexicon\Node;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\Node\NodeInterface;
use Anomaly\Lexicon\Node\NodeCollection;
use Anomaly\Lexicon\Node\NodeExtractor;
use Anomaly\Lexicon\Node\Variable;
use Anomaly\Lexicon\Stub\Node\Node;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class NodeFactorySpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Node
 */
class NodeFactorySpec extends ObjectBehavior
{

    function let(LexiconInterface $lexicon, NodeCollection $nodeCollection, NodeExtractor $nodeExtractor)
    {
        $this->beConstructedWith($lexicon, $nodeCollection, $nodeExtractor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Node\NodeFactory');
    }

    function it_can_get_lexicon()
    {
        $this->getLexicon()->shouldImplement('Anomaly\Lexicon\Contract\LexiconInterface');
    }

    function it_can_set_and_get_node_group()
    {
        $this->setNodeGroup('variables')->getNodeGroup()->shouldReturn('variables');
    }

    function it_can_set_and_get_node_types()
    {
        $this->getNodeTypes()->shouldBeArray();
    }

    function it_can_set_and_get_attribute_node_types()
    {
        $this->setAttributeNodeTypes([
                'Anomaly\Lexicon\Stub\Node\Node',
                'Anomaly\Lexicon\Stub\Node\Node2',
                'Anomaly\Lexicon\Stub\Node\Node3',
            ])->getAttributeNodeTypes()->shouldHaveCount(3);
    }

    function it_can_get_node_collection()
    {
        $this->getCollection()->shouldHaveType('Anomaly\Lexicon\Node\NodeCollection');
    }

    function it_can_add_node(Node $node, NodeCollection $nodeCollection)
    {
        $nodeCollection->push($node)->shouldBeCalled();
        $this->addNode($node);
    }

    function it_can_make_node_of_type(Variable $nodeType)
    {
        $nodeType->incrementDepth()->willReturn(true);
        $nodeType->setParentId(null)->shouldBeCalled();
        $nodeType->setMatch([])->shouldBeCalled();
        $this->make(
            $nodeType,
            $match = [],
            $parent = null,
            $offset = 0,
            $depth = 0
        )->shouldHaveType('Anomaly\Lexicon\Node\Variable');
    }

    function it_can_get_node_by_id(NodeCollection $nodeCollection)
    {
        $nodeCollection->getById('stub-id-1')->shouldBeCalled();
        $this->getById('stub-id-1');
    }
    
    function it_can_create_child_nodes(Node $node)
    {
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

}
