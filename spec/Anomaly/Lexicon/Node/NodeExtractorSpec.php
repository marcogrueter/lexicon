<?php namespace spec\Anomaly\Lexicon\Node;

use Anomaly\Lexicon\Contract\Node\NodeInterface;
use Anomaly\Lexicon\Contract\Node\BlockInterface;
use Anomaly\Lexicon\Node\NodeExtractor;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class NodeExtractorSpec
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Node
 */
class NodeExtractorSpec extends ObjectBehavior
{

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Node\NodeExtractor');
    }

    function it_can_extract_child_from_parent(NodeInterface $child, BlockInterface $parent)
    {
        $this->extract($child, $parent);
    }

    function it_can_extract_opening_content(BlockInterface $child, BlockInterface $parent)
    {
        $parent->getCurrentContent()->willReturn('{{ parent }}Hello{{ /parent }}');
        $child->getOpeningTag()->willReturn('{{ parent }}');
        $child->getExtractionId(NodeExtractor::OPENING_TAG)->willReturn('__opening__');
        $parent->setCurrentContent('__opening__Hello{{ /parent }}')->shouldBeCalled();
        $this->extractOpening($child, $parent);
    }

    function it_can_extract_closing_content(BlockInterface $child, BlockInterface $parent)
    {
        $parent->getCurrentContent()->willReturn('__opening__Hello{{ /parent }}');
        $child->getClosingTag()->willReturn('{{ /parent }}');
        $child->getExtractionId(NodeExtractor::CLOSING_TAG)->willReturn('__closing__');
        $parent->setCurrentContent("__opening__Hello__closing__")->shouldBeCalled();
        $this->extractClosing($child, $parent);
    }

    function it_can_extract_content(NodeInterface $child, BlockInterface $parent)
    {
        $parent->getCurrentContent()->willReturn('__opening__Hello__closing__');
        $child->getExtractionContent()->willReturn('Hello');
        $child->getExtractionId()->willReturn('__content__');
        $parent->setCurrentContent('__opening____content____closing__')->shouldBeCalled();
        $this->extractContent($child, $parent);
    }

    function it_can_inject_child_into_parent(NodeInterface $child, BlockInterface $parent)
    {
        $this->inject($child, $parent);
    }

    function it_can_inject_opening_content(BlockInterface $child, BlockInterface $parent)
    {
        $child->compileOpeningTag()->willReturn($source = 'foreach($items as $item):');
        $child->getExtractionId(NodeExtractor::OPENING_TAG)->willReturn('__opening__');
        $child->validate()->willReturn(true);
        $parent->getCurrentContent()->willReturn('__opening____content____closing__');
        $parent->setCurrentContent('<?php foreach($items as $item): ?>__content____closing__')->shouldBeCalled();
        $this->injectOpening($child, $parent);
    }

    function it_can_inject_closing_content(BlockInterface $child, BlockInterface $parent)
    {
        $child->compileClosingTag()->willReturn($source = 'endforeach;');
        $child->getExtractionId(NodeExtractor::CLOSING_TAG)->willReturn('__closing__');
        $child->validate()->willReturn(true);
        $parent->getCurrentContent()->willReturn('<?php foreach($items as $item): ?>__content____closing__');
        $parent->setCurrentContent('<?php foreach($items as $item): ?>__content__<?php endforeach; ?>')->shouldBeCalled();
        $this->injectClosing($child, $parent);
    }

    function it_can_inject_php_content(NodeInterface $child, BlockInterface $parent)
    {
        $child->compile()->willReturn($content = "echo 'Hello';");
        $child->isPhp()->willReturn(true);
        $child->getExtractionId()->willReturn('__content__');
        $child->validate()->willReturn(true);
        $parent->getCurrentContent()->willReturn('<?php foreach($items as $item): ?>__content__<?php endforeach; ?>');
        $parent->setCurrentContent('<?php foreach($items as $item): ?><?php echo \'Hello\'; ?><?php endforeach; ?>')
            ->shouldBeCalled();
        $this->injectContent($child, $parent);
    }

    function it_can_inject_non_php_content(NodeInterface $child, BlockInterface $parent)
    {
        $child->compile()->willReturn($content = '{{ ignoredTag1 }}{{ ignoredTag2 }}{{ ignoredTag3 }}');
        $child->isPhp()->willReturn(false);
        $child->getExtractionId()->willReturn('__content__');
        $child->validate()->willReturn(true);
        $parent->getCurrentContent()->willReturn('<?php foreach($items as $item): ?>__content__<?php endforeach; ?>');
        $parent->setCurrentContent('<?php foreach($items as $item): ?>{{ ignoredTag1 }}{{ ignoredTag2 }}{{ ignoredTag3 }}<?php endforeach; ?>')
            ->shouldBeCalled();
        $this->injectContent($child, $parent);
    }

    function it_can_prepare_regex_search()
    {
        $this->search('something')->shouldReturn('/something/');
    }
    
    function it_can_wrap_source_in_php_tags()
    {
        $this->php("echo 'Hello';")->shouldReturn("<?php echo 'Hello'; ?>");
    }
}
