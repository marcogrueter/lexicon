<?php namespace spec\Anomaly\Lexicon\Conditional\Expression;

use Anomaly\Lexicon\Contract\LexiconInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class LogicalOperatorNodeSpec
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Conditional\Expression
 */
class LogicalOperatorNodeSpec extends ObjectBehavior
{

    function let(LexiconInterface $lexicon)
    {
        $this->beConstructedWith($lexicon);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Conditional\Expression\LogicalOperatorNode');
    }

}
