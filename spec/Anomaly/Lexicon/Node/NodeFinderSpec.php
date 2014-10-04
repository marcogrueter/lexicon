<?php namespace spec\Anomaly\Lexicon\Node;

use Anomaly\Lexicon\Test\Spec;

/**
 * Class NodeFinderSpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Node
 */
class NodeFinderSpec extends Spec
{

    function let()
    {
        $this->beConstructedThrough('stub');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Node\NodeFinder');
    }

    function it_can_remove_prefix_from_name()
    {
        $this->getNode()->setName('.data.foo');
        $this->getName()->shouldReturn('foo');
    }

    function it_finds_item_source_with_root_alias()
    {
        $this->getNode()->setName('.data.foo');
        $this->getItemSource()->shouldReturn('$__data');
    }

    function it_finds_closest_item_source_if_alias_is_not_found()
    {
        $this->getNode()->setName('.none.name');
        $this->getItemSource()->shouldReturn('$booksItem');
    }

    function it_finds_a_distant_item_source()
    {
        $this->getNode()->setName('.library.name');
        $this->getItemSource()->shouldReturn('$librariesItem');

        $this->getNode()->setName('.school.name');
        $this->getItemSource()->shouldReturn('$schoolsItem');
    }

    function it_finds_the_closest_item_source_if_alias_was_not_provided()
    {
        $this->getItemSource()->shouldReturn('$booksItem');
    }

    function it_can_be_child_of_root()
    {
        $this->isChildOfRoot()->shouldBeBoolean();
    }

    function it_can_get_alias()
    {
        $this->getNode()->setName('.alias.foo.bar.baz');
        $this->getAlias()->shouldReturn('alias');
    }

    function it_can_get_alias_prefix()
    {
        $this->getNode()->setName('.alias.foo.bar.baz');
        $this->getAliasPrefix()->shouldReturn('.alias.');
    }

    function it_can_get_node_by_alias()
    {
        $this->getNode()->setName('.library.foo.bar.baz');

        $node = $this->getNodeByAlias();
        $node->shouldHaveType('Anomaly\Lexicon\Node\NodeType\Block');
        $node->getName()->shouldReturn('libraries');
    }

    function it_can_have_root_alias_prefix()
    {
        $this->hasRootAliasPrefix()->shouldBeBoolean();
    }

    function it_can_have_alias_prefix()
    {
        $this->hasAliasPrefix()->shouldBeBoolean();
    }

    function it_can_get_parent_node()
    {
        $this->getParent()->shouldImplement('Anomaly\Lexicon\Contract\Node\BlockInterface');
    }

    function it_get_lexicon()
    {
        $this->getLexicon()->shouldImplement('Anomaly\Lexicon\Contract\LexiconInterface');
    }

    function it_can_get_glue()
    {
        $this->glue()->shouldBeString();
    }

}
