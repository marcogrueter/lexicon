<?php namespace spec\Anomaly\Lexicon\Conditional\Expression;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class BooleanTestNodeSpec
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Conditional\Expression
 */
class BooleanTestNodeSpec extends ObjectBehavior
{

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Conditional\Expression\BooleanTestNode');
    }

}
