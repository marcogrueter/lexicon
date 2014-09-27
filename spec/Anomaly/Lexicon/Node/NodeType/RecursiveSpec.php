<?php namespace spec\Anomaly\Lexicon\Node\NodeType;

use Anomaly\Lexicon\Contract\LexiconInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class RecursiveSpec
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Node
 */
class RecursiveSpec extends ObjectBehavior
{

    function let(LexiconInterface $lexicon)
    {
        $this->beConstructedWith($lexicon);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Node\NodeType\Recursive');
    }

}
