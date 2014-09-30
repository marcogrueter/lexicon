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

    function let()
    {
        $this->beConstructedThrough('stub');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Conditional\Expression\BooleanTestNode');
    }

    function it_can_compile_source()
    {
        $this
            ->setCurrentContent('foo == bar')
            ->createChildNodes()
            ->compile()->shouldReturn('');
    }
    
}
