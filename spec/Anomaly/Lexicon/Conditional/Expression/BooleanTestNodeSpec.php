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

    function it_can_compile_boolean_test_source()
    {
        $this
            ->setContent('foo == bar')
            ->createChildNodes()
            ->compile()->shouldReturn("\$__data['__env']->booleanTest(\$__data['__env']->variable(\$__data, 'foo'),\$__data['__env']->variable(\$__data, 'bar'),'==')");
    }

    function it_can_compile_variable_source()
    {
        $this
            ->setContent('foo')
            ->createChildNodes()
            ->compile()->shouldReturn("\$__data['__env']->variable(\$__data, 'foo')");
    }

}
