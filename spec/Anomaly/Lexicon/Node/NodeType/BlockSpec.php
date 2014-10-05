<?php namespace spec\Anomaly\Lexicon\Node\NodeType;

use Anomaly\Lexicon\Stub\Node\Node;
use Anomaly\Lexicon\Stub\Node\Node2;
use Anomaly\Lexicon\Stub\Node\Node3;
use Anomaly\Lexicon\Test\Spec;

/**
 * Class BlockSpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Node
 */
class BlockSpec extends Spec
{

    function let()
    {
        $this->beConstructedThrough('stub');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Node\NodeType\Block');
    }

    function it_can_get_regex()
    {
        $this->regex()->shouldReturn('/(\{\{\s*([a-zA-Z0-9_\.]+)(\s.*?)\}\})(.*?)(\{\{\s*\/\2\s*\}\})/ms');
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

    function it_can_compile_php()
    {

        $this
            ->setCurrentContent('some-content')
            ->compile()
            ->shouldReturn('some-content');
    }

    function it_can_get_traversable_source()
    {
        $this->getTraversableSource()->shouldReturn("\$__data['__env']->variable(\$__data,'books',[],'',[],'traversable')");
    }

    function it_can_compile_opening_tag()
    {
        $this
            ->compileOpeningTag()
            ->shouldReturn("foreach(\$__data['__env']->variable(\$__data,'books',[],'',[],'traversable') as \$i=>\$booksItem):");
    }

    function it_can_compile_opening_tag_to_null_when_is_filter()
    {
        $this->setName('stub.md5');
        $this->compileOpeningTag()->shouldBe(null);
    }

    function it_can_compile_closing_tag()
    {
        $this->compileClosingTag()->shouldReturn('endforeach;');
    }

    function it_can_compile_closing_tag_to_null_when_is_filter()
    {
        $this->setName('stub.md5');
        $this->compileClosingTag()->shouldBe(null);
    }

    function it_can_compile_filter()
    {
        $this->setName('stub.md5');
        $this->compile();
        $this->compileFilter();
    }

    function it_can_compile_parse()
    {
        $this->setName('stub.uppercase');
        $this->compile();
        $this->compileParse();
    }

    function it_can_add_to_and_get_footer()
    {
        $this->addToFooter('foo');
        $this->getFooter()->shouldReturn(['foo']);
    }

    function it_can_compile_footer()
    {
        $this->addToFooter('that');
        $this->compileFooter('this plus ')->shouldReturn('this plus '.PHP_EOL.'that');
    }

    function it_can_compile_children(Node $child, Node2 $child2, Node3 $child3)
    {
        $this->setId('stub-parent-id');

        $factory = $this->getNodeFactory();

        $child->getId()->willReturn('stub-id-1');
        $child->setParentId("stub-parent-id")->shouldBeCalled();
        $child->deferCompile()->willReturn(false);
        $child->isExtractable()->willReturn(false);

        $child2->getId()->willReturn('stub-id-2');
        $child2->setParentId("stub-parent-id")->shouldBeCalled();
        $child2->deferCompile()->willReturn(false);
        $child2->isExtractable()->willReturn(false);

        $child3->getId()->willReturn('stub-id-3');
        $child3->setParentId("stub-parent-id")->shouldBeCalled();
        $child3->deferCompile()->willReturn(true);
        $child3->isExtractable()->willReturn(false);

        $factory->addNode($child);
        $factory->addNode($child2);
        $factory->addNode($child3);

        $this->addChild($child);
        $this->addChild($child2);
        $this->addChild($child3);

        $this->compileChildren();
    }

}
