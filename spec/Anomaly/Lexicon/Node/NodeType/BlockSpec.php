<?php namespace spec\Anomaly\Lexicon\Node\NodeType;

use Anomaly\Lexicon\Contract\LexiconInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class BlockSpec
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Node
 */
class BlockSpec extends ObjectBehavior
{

    function let(LexiconInterface $lexicon)
    {
        $this->beConstructedWith($lexicon);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Node\NodeType\Block');
    }

    function it_can_get_regex()
    {
        $this->regex()->shouldReturn('/(\{\{\s*()(\s.*?)\}\})(.*?)(\{\{\s*\/\2\s*\}\})/ms');
    }
    
    function it_can_setup_regex_match()
    {
        $this->setup();
    }

    function it_can_set_and_get_full_content()
    {
        $this->setFullContent('full content')->getFullContent()->shouldReturn('full content');
    }

    function it_can_set_and_get_opening_tag()
    {
        $this->setOpeningTag('{{ books }}')->getOpeningTag()->shouldReturn('{{ books }}');
    }

    function it_can_set_and_get_closing_tag()
    {
        $this->setClosingTag('{{ /books }}')->getClosingTag()->shouldReturn('{{ /books }}');
    }
    
    function it_can_compile_source()
    {
        $this->compile();
    }

    function it_can_compile_footer()
    {
        $this->compileFooter();
    }
    
    function it_can_compile_children()
    {
        $this->compileChildren();
    }

    function it_can_compile_opening_tag()
    {
        $this->compileOpeningTag();
    }

    function it_can_compile_closing_tag()
    {
        $this->compileClosingTag();
    }

    function it_can_compile_filter()
    {
        $this->compileFilter();
    }
    
    function it_can_compile_parse()
    {
        $this->compileParse();
    }
    
    function it_can_get_traversable_source()
    {

    }
    
    function it_can_add_to_and_get_footer()
    {
        $this->addToFooter('foo');
        $this->getFooter()->shouldReturn(['foo']);
    }
    
}
