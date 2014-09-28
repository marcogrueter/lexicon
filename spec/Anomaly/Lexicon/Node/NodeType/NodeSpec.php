<?php namespace spec\Anomaly\Lexicon\Node\NodeType;

use Anomaly\Lexicon\Contract\LexiconInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\CssSelector\Node\NodeInterface;

/**
 * Class NodeSpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Node
 */
class NodeSpec extends ObjectBehavior
{

    function let()
    {
        $this->beConstructedThrough('stub');
    }

    function it_is_instantiable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Node\NodeType\Node');
    }

    function it_can_set_and_get_match()
    {
        $this->setMatch([])->getMatch()->shouldBeArray();
    }

    function it_can_assert_it_is_php()
    {
        $this->isPhp()->shouldBeBoolean();
    }

    function it_can_assert_its_compilation_is_defered()
    {
        $this->deferCompile()->shouldBeBoolean();
    }

    function it_can_assert_it_is_extractable()
    {
        $this->isExtractable()->shouldBeBoolean();
    }

    function it_can_get_regex()
    {
        $this->regex()->shouldBeString();
    }

    function it_can_compile()
    {
        $this->compile()->shouldBeString();
    }

    function it_can_get_node_finder()
    {
        $this->getNodeFinder()->shouldHaveType('Anomaly\Lexicon\Node\NodeFinder');
    }

    function it_can_get_node_factory()
    {
        $this->getNodeFactory()->shouldHaveType('Anomaly\Lexicon\Node\NodeFactory');
    }

    function it_can_get_siblings()
    {
        $this->getSiblings()->shouldBeArray();
    }

    function it_can_get_first_sibling()
    {
        $this->getFirstSibling('if')->shouldImplement('Anomaly\Lexicon\Contract\Node\NodeInterface');
    }
    
    function it_can_get_its_position_within_the_content()
    {
        $this->getPosition()->shouldBeNumeric();
    }

}