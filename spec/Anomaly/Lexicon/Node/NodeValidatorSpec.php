<?php namespace spec\Anomaly\Lexicon\Node;

use Anomaly\Lexicon\Contract\Node\NodeInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class NodeValidatorSpec
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Node
 */
class NodeValidatorSpec extends ObjectBehavior
{

    function let()
    {
        $this->beConstructedThrough('stub');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Node\NodeValidator');
    }
    
    function it_can_get_node()
    {
        $this->getNode()->shouldImplement('Anomaly\Lexicon\Contract\Node\NodeInterface');
    }

    function it_can_be_valid()
    {
        $this->isValid()->shouldBeBoolean();
    }

    function it_can_count_siblings_with_name()
    {
        $this->countSiblings('if')->shouldReturn(2);
    }
    
    function it_can_assert_if_the_count_of_two_sibling_names_are_equal()
    {
        $this->isEqualCount('if', 'endif')->shouldBeBoolean();
    }

    function it_can_assert_if_is_has_siblings()
    {
        $this->hasSiblings('elseif')->shouldBeBoolean();
    }

    function it_can_assert_if_node_is_after_another()
    {
        $this->isAfter('if')->shouldReturn(true);
    }

}
