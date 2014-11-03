<?php namespace spec\Anomaly\Lexicon\Conditional\Expression;

use Anomaly\Lexicon\Test\Spec;

/**
 * Class BooleanTestNodeSpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Conditional\Expression
 */
class BooleanTestNodeSpec extends Spec
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
            ->compile()->shouldReturn(
                "\$this->booleanTest(\$this->variable(\$__data, 'foo'),\$this->variable(\$__data, 'bar'),'==')"
            );
    }

    function it_can_compile_variable_source()
    {
        $this
            ->setContent('foo')
            ->compile()->shouldReturn("\$this->variable(\$__data, 'foo')");
    }

}
