<?php namespace spec\Anomaly\Lexicon\Node\NodeType;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Test\Spec;

/**
 * Class SectionOverwriteSpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Node
 */
class SectionOverwriteSpec extends Spec
{

    function let(LexiconInterface $lexicon)
    {
        $this->beConstructedWith($lexicon);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Node\NodeType\SectionOverwrite');
    }

    function it_can_compile_stop_section_as_overwrite()
    {
        $this->compile()->shouldReturn("\$this->view()->stopSection(true);");
    }

}
