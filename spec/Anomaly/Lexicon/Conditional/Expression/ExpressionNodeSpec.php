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

    function let(LexiconInterface $lexicon)
    {
        $this->beConstructedWith($lexicon);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Conditional\Expression\ExpressionNode');
    }

    function it_can_get_logical_operators()
    {
        $this->getLogicalOperators()->shouldReturn(
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
        $this->regex()->shouldReturn('/(and|or|&&|\|\|)/');
    }

    function it_can_get_matches()
    {
        $this->getMatches('this or that and then or there && yes || no')->shouldReturn(
            [
                'this ',
                'or',
                ' that ',
                'and',
                ' then ',
                'or',
                ' there ',
                '&&',
                ' yes ',
                '||',
                ' no'
            ]
        );
    }

}
