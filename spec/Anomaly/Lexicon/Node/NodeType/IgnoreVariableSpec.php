<?php namespace spec\Anomaly\Lexicon\Node\NodeType;

use Anomaly\Lexicon\Contract\LexiconInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class IgnoreVariableSpec
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Node
 */
class IgnoreVariableSpec extends ObjectBehavior
{

    function let(LexiconInterface $lexicon)
    {
        $this->beConstructedWith($lexicon);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Node\NodeType\IgnoreVariable');
    }

    function it_can_get_regex()
    {
        $this->regex()->shouldReturn('/@(\{\{\s*(.*?)(\s.*?)?\s*(\/)?\}\})/ms');
    }

    function it_can_setup_regex_match()
    {
        $this->setup();
    }

}
