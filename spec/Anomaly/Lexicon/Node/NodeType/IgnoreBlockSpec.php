<?php namespace spec\Anomaly\Lexicon\Node\NodeType;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Test\Spec;

/**
 * Class IgnoreBlockSpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Node
 */
class IgnoreBlockSpec extends Spec
{

    function let(LexiconInterface $lexicon)
    {
        $this->beConstructedWith($lexicon);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Node\NodeType\IgnoreBlock');
    }

    function it_can_get_regex()
    {
        $this->regex()->shouldReturn('/\{\{\s*(ignore)(\s.*?)\}\}(.*?)\{\{\s*\/\1\s*\}\}/ms');
    }

    function it_can_setup_regex_match()
    {
        $this->setup();
    }

    function it_can_compile_content_as_is()
    {
        $this->setContent('{{ original }}');
        $this->compile()->shouldReturn('{{ original }}');
    }

}
