<?php namespace spec\Anomaly\Lexicon\Node\NodeType;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Test\Spec;
use Prophecy\Argument;

/**
 * Class ConditionalSpec
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Node
 */
class ConditionalSpec extends Spec
{

    function let()
    {
        $this->beConstructedThrough('stub');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Node\NodeType\Conditional');
    }

    function it_can_get_name_matcher()
    {
        $this->getNameMatcher()->shouldReturn('if|elseif|unless|elseunless');
    }

    function it_can_setup_regex_match()
    {
        $this->setup();
    }

    function it_can_get_construct_name()
    {
        $this->setName('unless');
        $this->getConstructName()->shouldReturn('if');
        $this->setName('elseunless');
        $this->getConstructName()->shouldReturn('elseif');
    }

    function it_can_get_validator()
    {
        $this->getValidator()->shouldImplement('Anomaly\Lexicon\Contract\Node\ValidatorInterface');
    }
    
    function it_can_compile_php()
    {
        $this
            ->setName('if')
            ->setContent('foo or bar and baz && ying || yang')
            ->compile()
            ->shouldReturn("if(\$__data['__env']->variable(\$__data, 'foo') or \$__data['__env']->variable(\$__data, 'bar') and \$__data['__env']->variable(\$__data, 'baz') && \$__data['__env']->variable(\$__data, 'ying') || \$__data['__env']->variable(\$__data, 'yang')):");
    }

    function it_can_compile_unless_to_php()
    {
        $this
            ->setName('unless')
            ->setContent('foo or bar and baz && ying || yang')
            ->compile()
            ->shouldReturn("if(!(\$__data['__env']->variable(\$__data, 'foo') or \$__data['__env']->variable(\$__data, 'bar') and \$__data['__env']->variable(\$__data, 'baz') && \$__data['__env']->variable(\$__data, 'ying') || \$__data['__env']->variable(\$__data, 'yang'))):");
    }
    
}
