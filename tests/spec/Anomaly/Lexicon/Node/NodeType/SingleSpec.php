<?php namespace spec\Anomaly\Lexicon\Node\NodeType;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Test\Spec;

/**
 * Class SingleSpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Node\NodeType
 */
class SingleSpec extends Spec
{

    function let(LexiconInterface $lexicon)
    {
        $this->beConstructedWith($lexicon);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Node\NodeType\Single');
    }

    function it_can_get_regex()
    {
        $this->regex()->shouldReturn('/\{\{\s*(root)(\s.*?)?\s*(\/)?\}\}/ms');
    }

    function it_can_setup_regex_matches()
    {
        $this->setup();
    }

}
