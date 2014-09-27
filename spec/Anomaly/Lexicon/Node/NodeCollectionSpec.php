<?php namespace spec\Anomaly\Lexicon\Node;

use Anomaly\Lexicon\Stub\Node\Node;
use Anomaly\Lexicon\Stub\Node\Node2;
use Anomaly\Lexicon\Stub\Node\Node3;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class NodeCollectionSpec
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Node
 */
class NodeCollectionSpec extends ObjectBehavior
{

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Node\NodeCollection');
    }

    function it_can_get_node_by_id(Node $node1, Node2 $node2, Node3 $node3)
    {
        $this->push($node1);
        $this->push($node2);
        $this->push($node3);

        $node1->getId()->willReturn('stub-id-1');
        $node2->getId()->willReturn('stub-id-2');
        $node3->getId()->willReturn('stub-id-3');

        $this
            ->getById('stub-id-3')
            ->shouldHaveType('Anomaly\Lexicon\Stub\Node\Node3');
    }
    
    function it_can_get_multiple_nodes_by_id(Node $node1, Node2 $node2, Node3 $node3)
    {
        $this->push($node1);
        $this->push($node2);
        $this->push($node3);

        $node1->getId()->willReturn('stub-id-1');
        $node2->getId()->willReturn('stub-id-2');
        $node3->getId()->willReturn('stub-id-3');

        $this->getByIds(['stub-id-1', 'stub-id-2'])->shouldHaveCount(2);
    }

}
