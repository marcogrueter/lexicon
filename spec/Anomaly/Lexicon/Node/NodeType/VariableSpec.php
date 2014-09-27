<?php namespace spec\Anomaly\Lexicon\Node\NodeType;

use Anomaly\Lexicon\Attribute\AttributeNode;
use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Lexicon;
use Anomaly\Lexicon\Node\NodeFactory;
use Anomaly\Lexicon\Node\NodeFinder;
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

    function let()
    {
        $this->beConstructedThrough('stub');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Node\NodeType\Variable');
    }

    function it_can_get_regex()
    {
        $this->regex()->shouldReturn('/\{\{\s*([a-zA-Z0-9_\.]+)(\s+.*?)?\s*(\/)?\}\}/ms');
    }

    function it_can_compile_variable(AttributeNode $attributeNode, NodeFinder $nodeFinder, NodeFactory $nodeFactory)
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
