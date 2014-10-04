<?php namespace spec\Anomaly\Lexicon\Node\NodeType;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Test\Spec;

/**
 * Class ConditionalElseSpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Node
 */
class ConditionalElseSpec extends Spec
{

    function let(LexiconInterface $lexicon)
    {
        $this->beConstructedWith($lexicon);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Node\NodeType\ConditionalElse');
    }

    function it_can_get_name_matcher()
    {
        $this->getNameMatcher()->shouldReturn('else');
    }

    function it_can_setup_regex_match()
    {
        $this->setup();
    }

    function it_can_compile_conditional_else()
    {
        $this->compile()->shouldReturn('else:');
    }
}
