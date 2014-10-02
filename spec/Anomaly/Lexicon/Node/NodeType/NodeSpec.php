<?php namespace spec\Anomaly\Lexicon\Node\NodeType;

use Anomaly\Lexicon\Test\Spec;

/**
 * Class NodeSpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Node
 */
class NodeSpec extends Spec
{

    function let()
    {
        $this->beConstructedThrough('stub');
    }

    function it_is_instantiable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Node\NodeType\Node');
    }

    function it_can_set_and_get_match()
    {
        $this->setMatch([])->getMatch()->shouldBeArray();
    }

    function it_can_assert_it_is_php()
    {
        $this->isPhp()->shouldBeBoolean();
    }

    function it_can_assert_its_compilation_is_defered()
    {
        $this->deferCompile()->shouldBeBoolean();
    }

    function it_can_assert_it_is_extractable()
    {
        $this->isExtractable()->shouldBeBoolean();
    }

    function it_can_get_regex()
    {
        $this->regex()->shouldBeString();
    }

    function it_can_compile()
    {
        $this->compile()->shouldBeString();
    }

    function it_can_get_node_finder()
    {
        $this->getNodeFinder()->shouldHaveType('Anomaly\Lexicon\Node\NodeFinder');
    }

    function it_can_get_node_factory()
    {
        $this->getNodeFactory()->shouldHaveType('Anomaly\Lexicon\Node\NodeFactory');
    }

    function it_can_get_siblings()
    {
        $this->getSiblings()->shouldBeArray();
    }

    function it_can_get_first_sibling()
    {
        $this->getFirstSibling('if')->shouldImplement('Anomaly\Lexicon\Contract\Node\NodeInterface');
    }

    function it_can_get_its_position_within_the_content()
    {
        $this->getPosition()->shouldBeNumeric();
    }

    function it_can_get_item_alias_from_raw_attributes()
    {
        $this
            ->setRawAttributes('foo="bar" as book')
            ->getItemAliasFromRawAttributes()
            ->shouldReturn('book');
    }

    function it_can_set_and_get_item_alias()
    {
        $this->setItemAlias('book')->getItemAlias()->shouldReturn('book');
    }

    function it_can_get_extraction_id()
    {
        $this
            ->setId(md5('foo'))
            ->setName('baz')
            ->getExtractionId('suffix')
            ->shouldReturn('Anomaly\Lexicon\Stub\Node\Node__baz__acbd18db4cc2f85cedef654fccc4a4d8__suffix__');
    }

    function it_can_get_attribute_node()
    {
        $this->getAttributeNode()->shouldHaveType('Anomaly\Lexicon\Attribute\AttributeNode');
    }

    function it_can_compile_attributes()
    {
        $this
            ->setRawAttributes('foo="bar"')
            ->compileAttributes("['foo'=>'bar']");
    }

    function it_can_compile_attribute_value()
    {
        $this
            ->setRawAttributes('foo="bar"')
            ->compileAttributeValue('foo')->shouldReturn("'bar'");
    }

    function it_can_set_and_get_content()
    {
        $this->setContent('foo')->getContent()->shouldReturn('foo');
    }

    function it_can_set_and_get_extraction_content()
    {
        $this
            ->setExtractionContent('extract_this')
            ->getExtractionContent()
            ->shouldReturn('extract_this');
    }

    function it_can_set_and_get_offset()
    {
        $this->setOffset(5)->getOffset()->shouldBe(5);
    }

    function it_can_get_name_regex_matcher()
    {
        $this->setName('foo')->getNameMatcher()->shouldReturn('foo');
    }

    function it_can_be_root()
    {
        $this->isRoot()->shouldBeBoolean();
    }

    function it_can_get_item_source()
    {
        $this->setName('books')->getItemSource()->shouldReturn('$booksItem');
    }

    function it_can_get_root_node()
    {
        $this->getRootNode()->shouldImplement('Anomaly\Lexicon\Contract\Node\RootInterface');
    }

    function it_can_get_single_tag_matches()
    {
        $this->setName('tag')->getSingleTagMatches('{{ tag }}')->shouldHaveCount(1);
    }

    function it_can_wrap_source_in_espape_function()
    {
        $this->escape('$var')->shouldReturn('e($var)');
    }

    function it_can_get_validator()
    {
        $this->getValidator();
    }

    function it_can_validate()
    {
        $this->validate()->shouldBeBoolean();
    }

    function it_can_be_valid()
    {
        $this->isValid()->shouldBeBoolean();
    }

    function it_can_be_filter()
    {
        $this->isFilter()->shouldBeBoolean();
    }

    function it_can_be_parse_able()
    {
        $this->isParse()->shouldBeBoolean();
    }
    
    function it_can_set_and_get_depth()
    {
        $this->setDepth(8)->getDepth()->shouldBe(8);
    }
    
    function it_can_get_single_match()
    {
        $this->getSingleMatch('foo bar baz', '/\bbaz\b/')->shouldReturn(['baz']);
    }

    function it_can_have_parent_block()
    {
        $this->hasParentBlock()->shouldBeBoolean();
    }

    function it_can_have_parent_root()
    {
        $this->hasParentRoot()->shouldBeBoolean();
    }
    
    function it_can_have_parent_block_that_is_not_root()
    {
        $this->hasParentBlockNotRoot()->shouldBeBoolean();
    }

}
