<?php namespace spec\Anomaly\Lexicon\View;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Illuminate\Filesystem\Filesystem;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class CompilerSpec
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\View
 */
class CompilerSpec extends ObjectBehavior
{

    function let()
    {
        $this->beConstructedThrough('stub');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\View\Compiler');
    }

    function it_can_set_and_get_path()
    {
        $this->setPath('foo')->getPath()->shouldReturn('foo');
    }

    function it_can_set_and_get_hash()
    {
        $this->setHash(md5('foo'))->getHash()->shouldReturn(md5('foo'));
    }

    function it_can_set_and_get_lexicon(LexiconInterface $lexicon)
    {
        $this->setLexicon($lexicon)->getLexicon()->shouldHaveType('Anomaly\Lexicon\Contract\LexiconInterface');
    }

    function it_can_check_if_a_parsed_view_is_not_compiled()
    {
        $this->isNotParsed('nonexistent')->shouldReturn(true);
    }

    function it_can_compile_view_template()
    {
        $this->compile($this->path('hello.html'));
    }
    
    function it_can_compile_string_template()
    {
        $this->compileParse('<h1>Hello {{ name }}!</h1>');
    }

    public function path($path)
    {
        return __DIR__ . '/../../../../resources/views/' . $path;
    }

}
