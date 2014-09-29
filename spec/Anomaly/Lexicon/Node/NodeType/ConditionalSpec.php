<?php namespace spec\Anomaly\Lexicon\Node\NodeType;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Test\Spec;
use Prophecy\Argument;

/**
 * Class ConditionalSpec
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Node
 */
class ConditionalSpec extends Spec
{

    function let(LexiconInterface $lexicon)
    {
        $this->beConstructedWith($lexicon);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Node\NodeType\Conditional');
    }

    function it_can_get_construct_name()
    {
        $this->setName('unless');
        $this->getConstructName()->shouldReturn('if');
        $this->setName('elseunless');
        $this->getConstructName()->shouldReturn('elseif');
    }

}
