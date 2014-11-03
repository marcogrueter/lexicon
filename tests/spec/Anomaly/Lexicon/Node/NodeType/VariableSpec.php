<?php namespace spec\Anomaly\Lexicon\Node\NodeType;

use Anomaly\Lexicon\Attribute\AttributeNode;
use Anomaly\Lexicon\Lexicon;
use Anomaly\Lexicon\Node\NodeFactory;
use Anomaly\Lexicon\Node\NodeFinder;
use Anomaly\Lexicon\Test\Spec;

/**
 * Class VariableSpec
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Node
 */
class VariableSpec extends Spec
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
    
    function it_can_compile_variable_php()
    {
        $this->setName('foo');
        $this->compileVariable(false)->shouldReturn("e(\$this->variable(\$__data,'foo',[],'',null,'string'))");
    }

    function it_can_compile_echo_variable_php()
    {
        $this->setName('foo');
        $this->compileVariable(true)->shouldReturn("echo e(\$this->variable(\$__data,'foo',[],'',null,'string'));");
    }

    function it_can_compile_unescaped_variable_php()
    {
        $this->setName('foo');
        $this->compileVariable(true, false)->shouldReturn("echo \$this->variable(\$__data,'foo',[],'',null,'string');");
    }

    function it_can_compile_php()
    {
        $this->setName('foo');
        $this->compile()->shouldReturn("echo e(\$this->variable(\$__data,'foo',[],'',null,'string'));");
    }

    function it_can_compile_key()
    {
        $this->compileKey()->shouldBeNumeric();
    }

}
