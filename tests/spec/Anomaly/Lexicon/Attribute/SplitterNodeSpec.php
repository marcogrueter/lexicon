<?php namespace spec\Anomaly\Lexicon\Attribute;

use Anomaly\Lexicon\Test\Spec;

/**
 * Class SplitterNodeSpec
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Attribute
 */
class SplitterNodeSpec extends Spec
{

    function let()
    {
        $this->beConstructedThrough('stub');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Attribute\SplitterNode');
    }

    function it_can_get_regex()
    {
        $this->regex()->shouldReturn('/(\{.+\})/ms');
    }
    
    function it_can_get_matches()
    {
        $this->getMatches('foo bar {baz}')->shouldHaveCount(3);
    }
    
    function it_can_create_child_nodes()
    {
        $this->setContent('foo {bar} baz')
            ->createChildNodes()
            ->getChildren()
            ->shouldHaveNodeCount(3);
    }
    
    function it_can_detect_delimiter_nodes()
    {
        $this->detect('{delimiter}')->shouldBe(true);
    }

    function it_can_get_delimiter_node()
    {
        $this->getDelimiterNode()->shouldImplement('Anomaly\Lexicon\Contract\Node\NodeInterface');
    }

    function it_can_get_segment_node()
    {
        $this->getSegmentNode()->shouldImplement('Anomaly\Lexicon\Contract\Node\NodeInterface');
    }

    function it_can_compile_php()
    {
        $this->setContent('foo {bar} baz')
            ->createChildNodes()
            ->compile()
            ->shouldReturn("'foo '.\$this->variable(\$__data,'bar',[],'',null,'string').' baz'");
    }
    
}
