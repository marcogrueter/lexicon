<?php namespace spec\Anomaly\Lexicon\Node;

use Anomaly\Lexicon\Stub\Node\Node;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class NodeFinderSpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Node
 */
class NodeFinderSpec extends ObjectBehavior
{

    function let(Node $node)
    {
        $this->beConstructedWith($node);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Node\NodeFinder');
    }

}
