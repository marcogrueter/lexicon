<?php namespace spec\Anomaly\Lexicon\Node\NodeType;

use Anomaly\Lexicon\Contract\LexiconInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class ConditionalEndifSpec
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Node
 */
class ConditionalEndifSpec extends ObjectBehavior
{

    function let(LexiconInterface $lexicon)
    {
        $this->beConstructedWith($lexicon);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Node\NodeType\ConditionalEndif');
    }

    function it_can_setup_regex_match()
    {
        $this->setup();
    }
    
    function it_can_compile_endif()
    {
        $this->compile()->shouldReturn('endif;');
    }
    
}
