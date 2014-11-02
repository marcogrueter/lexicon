<?php namespace spec\Anomaly\Lexicon\Node\NodeType;

use Anomaly\Lexicon\Attribute\AttributeNode;
use Anomaly\Lexicon\Test\Spec;

/**
 * Class SectionYieldSpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Node
 */
class SectionYieldSpec extends Spec
{

    function let()
    {
        $this->beConstructedThrough('stub');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Node\NodeType\SectionYield');
    }

    function it_can_compile_yield_content(AttributeNode $attributeNode)
    {
        $this->compile()->shouldReturn("echo \$__data['__env']->yieldContent('foo');");
    }

}
