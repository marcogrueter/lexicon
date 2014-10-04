<?php namespace spec\Anomaly\Lexicon\Node\NodeType;

use Anomaly\Lexicon\Test\Spec;

/**
 * Class BreaksSpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Node
 */
class BreaksSpec extends Spec
{

    function let()
    {
        $this->beConstructedThrough('stub');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Node\NodeType\Breaks');
    }

    function it_can_be_validated()
    {
        $this->isValid()->shouldBeBoolean();
    }

    function it_can_compile_break()
    {
        $this->compile()->shouldReturn('break;');
    }

}
