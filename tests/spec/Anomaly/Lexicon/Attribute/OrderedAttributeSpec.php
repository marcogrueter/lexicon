<?php namespace spec\Anomaly\Lexicon\Attribute;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Test\Spec;

/**
 * Class OrderedAttributeSpec
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Attribute
 */
class OrderedAttributeSpec extends Spec
{

    function let()
    {
        $this->beConstructedThrough('stub');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Attribute\OrderedAttribute');
    }

    function it_can_get_regex()
    {
        $this->regex()->shouldReturn('/\s*(\'|"|&#?\w+;)(.*?)(?<!\\\\)\1/ms');
    }
    
    function it_can_get_single_quote_matches()
    {
        $this->getMatches(" 'value1' 'value2' ")->shouldHaveCount(2);
    }

    function it_can_get_double_quote_matches()
    {
        $this->getMatches(' "value1" "value2" ')->shouldHaveCount(2);
    }
    
    function it_can_setup_regex_match()
    {
        $this->setup();
    }

    function it_can_get_key()
    {
        $this->setOffset(1)->getKey()->shouldReturn(1);
    }

    function it_can_compile_key()
    {
        $this->setOffset(2)->compileKey()->shouldReturn(2);
    }
    
}
