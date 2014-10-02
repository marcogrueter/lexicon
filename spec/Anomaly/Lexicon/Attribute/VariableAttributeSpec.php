<?php namespace spec\Anomaly\Lexicon\Attribute;

use Anomaly\Lexicon\Test\Spec;

/**
 * Class VariableAttributeSpec
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Attribute
 */
class VariableAttributeSpec extends Spec
{

    function let()
    {
        $this->beConstructedThrough('stub');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Attribute\VariableAttribute');
    }

    function it_can_get_regex()
    {
        $this->regex()->shouldReturn('/\{\s*([a-zA-Z0-9_\.]+)(\s+.*?)?\s*(\/)?\}/ms');
    }
    
    function it_can_setup_regex_match()
    {
        $this->setup();
    }

    function it_can_compile_value()
    {
        $this
            ->setValue('foo')
            ->compileValue()
            ->shouldReturn("\$__data['__env']->variable(\$__data,'foo',[],'',null,'echo')");
    }

}
