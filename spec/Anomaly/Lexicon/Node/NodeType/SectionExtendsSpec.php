<?php namespace spec\Anomaly\Lexicon\Node\NodeType;

use Anomaly\Lexicon\Test\Spec;

/**
 * Class SectionExtendsSpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Node
 */
class SectionExtendsSpec extends Spec
{

    function let()
    {
        $this->beConstructedThrough('stub');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Node\NodeType\SectionExtends');
    }

    function it_can_compile_php()
    {
        $this->setRawAttributes('layout="test::foo"')->compile();
        $this->getRootNode()->getFooter()->shouldReturn(
            [
                "<?php echo \$__data['__env']->make('test::foo',\$__data)->render(); ?>"
            ]
        );
    }

}
