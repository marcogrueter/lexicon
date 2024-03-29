<?php namespace spec\Anomaly\Lexicon\Conditional\Expression;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Test\Spec;

/**
 * Class ExpressionNodeSpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Conditional\Expression
 */
class ExpressionNodeSpec extends Spec
{

    function let()
    {
        $this->beConstructedThrough('stub');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Conditional\Expression\ExpressionNode');
    }

    function it_can_get_logical_operators()
    {
        $this->getOperators()->shouldReturn(
            [
                'and',
                'or',
                '&&',
                '||',
            ]
        );
    }

    function it_can_get_regex()
    {
        $this->regex()->shouldReturn('/(\band\b|\bor\b|&&|\|\|)/');
    }

    function it_can_get_matches()
    {
        $this->getMatches('foo or bar and baz && yin || yang')->shouldReturn(
            [
                'foo ',
                'or',
                ' bar ',
                'and',
                ' baz ',
                '&&',
                ' yin ',
                '||',
                ' yang'
            ]
        );
    }

    function it_can_get_child_nodes()
    {
        $this
            ->setContent('foo or bar and baz && ying || yang')
            ->createChildNodes()
            ->getChildren()
            ->shouldHaveNodeCount(9);
    }
    
    function it_can_compile_php()
    {
        $this
            ->setContent('foo or bar and baz')
            ->createChildNodes()
            ->compile()
            ->shouldReturn("\$this->variable(\$__data, 'foo') or \$this->variable(\$__data, 'bar') and \$this->variable(\$__data, 'baz')");
    }

}
