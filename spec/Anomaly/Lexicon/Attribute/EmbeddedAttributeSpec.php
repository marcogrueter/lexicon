<?php namespace spec\Anomaly\Lexicon\Attribute;

use Anomaly\Lexicon\Test\Spec;

/**
 * Class EmbeddedAttributeSpec
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Attribute
 */
class EmbeddedAttributeSpec extends Spec
{

    function let()
    {
        $this->beConstructedThrough('stub');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Attribute\EmbeddedAttribute');
    }
    
    function it_can_setup_regex_match()
    {
        $this->setup();
    }

}
