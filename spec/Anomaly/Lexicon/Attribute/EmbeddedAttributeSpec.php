<?php namespace spec\Anomaly\Lexicon\Attribute;

use Anomaly\Lexicon\Test\Spec;

/**
 * Class EmbeddedAttributeSpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
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

    function it_can_get_variable_and_attributes()
    {
        $this
            ->setContent("{ name 'value1' 'value2' }")
            ->getNameAndRawAttributes()
            ->shouldReturn(
            [
                "{ name 'value1' 'value2' }",
                "name",
                " 'value1' 'value2'",
            ]
        );
    }
    
    function it_can_compile_named_attributes_from_array()
    {
        $this
            ->setContent("foo='bar' yin='yang'")
            ->createChildNodes()
            ->compileSourceFromArray()
            ->shouldReturn("['foo'=>'bar','yin'=>'yang']");
    }

    function it_can_compile_ordered_attributes_from_array()
    {
        $this
            ->setContent("'value1' 'value2'")
            ->createChildNodes()
            ->compileSourceFromArray()
            ->shouldReturn("[0=>'value1',1=>'value2']");
    }

}
