<?php namespace spec\Anomaly\Lexicon\Node;

use Anomaly\Lexicon\Stub\Node\Node;
use Anomaly\Lexicon\Stub\Node\Node2;
use Anomaly\Lexicon\Stub\Node\Node3;
use Anomaly\Lexicon\Test\Spec;

/**
 * Class NodeCollectionSpec
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Node
 */
class NodeCollectionSpec extends Spec
{

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Node\NodeCollection');
    }

    function it_can_get_node_by_id(Node $node1, Node2 $node2, Node3 $node3)
    {
        $this->put('stub-id-1', $node1);
        $this->put('stub-id-2', $node2);
        $this->put('stub-id-3', $node3);

        $this
            ->getById('stub-id-3')
            ->shouldHaveType('Anomaly\Lexicon\Stub\Node\Node3');
    }
    
    function it_can_get_multiple_nodes_by_id(Node $node1, Node2 $node2, Node3 $node3)
    {
        $this->put('stub-id-1', $node1);
        $this->put('stub-id-2', $node2);
        $this->put('stub-id-3', $node3);

        $this->getByIds(['stub-id-1', 'stub-id-2'])->shouldHaveCount(2);
    }

}
