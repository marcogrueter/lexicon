<?php namespace spec\Anomaly\Lexicon\Node\NodeType;

use Anomaly\Lexicon\Test\Spec;

/**
 * Class IncludesSpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Node
 */
class IncludesSpec extends Spec
{

    function let()
    {
        $this->beConstructedThrough('stub');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Node\NodeType\Includes');
    }

    function it_can_compile_php()
    {
        $this
            ->setRawAttributes('partial="baz" share="title,author"')
            ->compile()
            ->shouldReturn("echo \$this->view()->make('baz',array_merge(\$__data,['title'=>\$this->variable(\$__data,'title',[],'',null,'string'),'author'=>\$this->variable(\$__data,'author',[],'',null,'string')]))->render();");
    }

}
