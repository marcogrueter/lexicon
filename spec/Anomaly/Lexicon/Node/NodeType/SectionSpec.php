<?php namespace spec\Anomaly\Lexicon\Node\NodeType;

use Anomaly\Lexicon\Test\Spec;

/**
 * Class SectionSpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Node
 */
class SectionSpec extends Spec
{

    function let()
    {
        $this->beConstructedThrough('stub');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Node\NodeType\Section');
    }

    function it_can_compile_php()
    {
        $this
            ->setRawAttributes('name="foo"')
            ->compile()
            ->shouldReturn("\$__data['__env']->startSection('foo');");
    }
    
}
