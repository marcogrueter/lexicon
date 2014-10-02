<?php namespace spec\Anomaly\Lexicon\Attribute;

use Anomaly\Lexicon\Test\Spec;

/**
 * Class StringAttributeSpec
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Attribute
 */
class StringAttributeSpec extends Spec
{

    function let()
    {
        $this->beConstructedThrough('stub');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Attribute\StringAttribute');
    }
    
    function it_can_setup_regex_match()
    {
        $this->setup();
    }

    function it_can_compile_php()
    {
        $this->setContent('string')->compile()->shouldReturn("'string'");
    }
    
}
