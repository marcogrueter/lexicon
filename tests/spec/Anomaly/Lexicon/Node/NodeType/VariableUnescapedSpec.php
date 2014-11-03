<?php namespace spec\Anomaly\Lexicon\Node\NodeType;

use Anomaly\Lexicon\Test\Spec;

/**
 * Class VariableUnescapedSpec
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Node\NodeType
 */
class VariableUnescapedSpec extends Spec
{

    function let()
    {
        $this->beConstructedThrough('stub');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Node\NodeType\VariableUnescaped');
    }

    function it_can_get_regex()
    {
        $this->regex()->shouldReturn('/\{\{\{\s*([a-zA-Z0-9_\.]+)(\s+.*?)?\s*(\/)?\}\}\}/ms');
    }
    
    function it_can_compile_php()
    {
        $this->setName('foo');
        $this->compile()->shouldReturn("echo \$this->variable(\$__data,'foo',[],'',null,'string');");
    }
    
}
