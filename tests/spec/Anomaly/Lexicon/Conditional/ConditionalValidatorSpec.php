<?php namespace spec\Anomaly\Lexicon\Conditional;

use Anomaly\Lexicon\Contract\Node\ConditionalInterface;
use Anomaly\Lexicon\Test\Spec;

/**
 * Class ConditionalValidatorSpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Conditional
 */
class ConditionalValidatorSpec extends Spec
{

    function let(ConditionalInterface $node)
    {
        $this->beConstructedWith($node);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Conditional\ConditionalValidator');
    }

/*    function it_can_validate_node_for_compilation(ConditionalInterface $node)
    {
        //$node->getConstructName()->willReturn('if');
        //$node->getSiblings()->shouldBeCalled();
        //$this->isValid()->shouldBeBoolean();
    }*/

/*    function it_can_validate_if_construct(ConditionalInterface $node)
    {
        //$node->getConstructName()->willReturn('if');
        //$node->getSiblings()->shouldBeCalled();
        //$this->isValidIf()->shouldBeBoolean();
    }*/

/*    function it_can_validate_elseif_construct(ConditionalInterface $node)
    {
        //$node->getConstructName()->willReturn('elseif');
        //$node->getSiblings()->shouldBeCalled();
        //$node->getFirstSibling('if')->shouldBeCalled();
        //$node->getPosition()->shouldBeCalled();
        //$this->isValidElseif()->shouldBeBoolean();
    }*/

/*    function it_can_validate_else_construct(ConditionalInterface $node)
    {
        //$node->getConstructName()->willReturn('else');
        //$node->getSiblings()->shouldBeCalled();
        //$this->isValidElse()->shouldBeBoolean();
    }*/

/*    function it_can_validate_endif_construct(ConditionalInterface $node)
    {
        //$node->getConstructName()->willReturn('endif');
        //$node->getSiblings()->shouldBeCalled();
        //$node->getFirstSibling('endif')->shouldBeCalled();
        //$this->isValidEndif()->shouldBeBoolean();
    }*/

}
