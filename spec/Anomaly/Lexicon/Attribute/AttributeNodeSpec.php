<?php namespace spec\Anomaly\Lexicon\Attribute;

use Anomaly\Lexicon\Stub\Node\Node;
use Anomaly\Lexicon\Test\Spec;

/**
 * Class AttributeNodeSpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Attribute
 */
class AttributeNodeSpec extends Spec
{

    function let()
    {
        $this->beConstructedThrough('stub');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Attribute\AttributeNode');
    }

    function it_can_setup_regex_match(Node $parent)
    {
        $this->setup();
    }

    function it_can_detect_named_attributes()
    {
        $this->detect('foo="bar"')->shouldBe(true);
    }

    function it_can_get_detected_attribute_node_type()
    {
        $this
            ->setCurrentContent('foo="bar"')
            ->getAttributeNodeType()
            ->shouldHaveType('Anomaly\Lexicon\Attribute\NamedAttribute');
    }

    function it_can_create_child_nodes()
    {
        $this->setCurrentContent('foo="bar" yin="yang"')
            ->createChildNodes()
            ->getChildren()
            ->shouldHaveNodeCount(2);
    }

    function it_can_create_a_single_child_node(Node $nodeType)
    {
        $this
            ->createChildNode($nodeType, [])
            ->getChildren()
            ->shouldHaveNodeCount(1);
    }
    
    function it_can_can_set_and_get_key()
    {
        $this->setKey('foo')->getKey()->shouldReturn('foo');
    }

    function it_can_set_and_get_value()
    {
        $this->setValue('baz')->getValue()->shouldReturn('baz');
    }
    
    function it_can_compile_key()
    {
        $this->setKey('foo')->compileKey()->shouldReturn("'foo'");
    }
    
    function it_can_compile_literal()
    {
        $this->setValue('yang')->compileLiteral()->shouldReturn("'yang'");
    }
    
    function it_can_compile_value()
    {
        $this->setValue('yang')->compileValue()->shouldReturn("'yang'");
    }
    
    function it_can_compile_array()
    {
        $this
            ->setCurrentContent('foo="bar" yin="yang"')
            ->createChildNodes()
            ->compileArray()
            ->shouldReturn([
                   "'foo'" => "'bar'",
                    "'yin'" => "'yang'"
                ]);
    }

    function it_can_compile_named_attribute_value()
    {
        $this
            ->setCurrentContent('foo="bar" yin="yang"')
            ->createChildNodes()
            ->compileAttributeValue('foo')->shouldReturn("'bar'");
    }
    
    function it_can_compile_ordered_attribute_value()
    {
        $this
            ->setCurrentContent('"bar" "yang"')
            ->createChildNodes()
            ->compileAttributeValue('', 1)->shouldReturn("'yang'");
    }

}
