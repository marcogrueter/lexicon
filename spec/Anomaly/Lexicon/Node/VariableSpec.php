<?php namespace spec\Anomaly\Lexicon\Node;

use Anomaly\Lexicon\Attribute\AttributeNode;
use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Lexicon;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;


/**
 * Class VariableSpec
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Node
 */
class VariableSpec extends ObjectBehavior
{

    function let(LexiconInterface $lexicon)
    {
        $this->beConstructedWith($lexicon);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Node\Variable');
    }

    function it_can_get_regex()
    {
        $this->regex()->shouldReturn('/\{\{\s*([a-zA-Z0-9_\.]+)(\s+.*?)?\s*(\/)?\}\}/ms');
    }

    function it_can_compile_variable(AttributeNode $attributeNode)
    {
        $this->setName('foo');
        $expected = Lexicon::EXPECTED_ECHO;
        $this->compile()->shouldReturn("echo \$__data['__env']->variable(\$__data,'foo',[],'',null,'{$expected}');");
    }

    function it_can_compile_key()
    {
        $this->compileKey()->shouldBeNumeric();
    }
}
