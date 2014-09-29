<?php namespace spec\Anomaly\Lexicon\Conditional\Validator;

use Anomaly\Lexicon\Contract\Node\NodeInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class EndifValidatorSpec
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Conditional\Validator
 */
class EndifValidatorSpec extends ObjectBehavior
{

    function let(NodeInterface $node)
    {
        $this->beConstructedWith($node);
    }
    
    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Conditional\Validator\EndifValidator');
    }
    
    function it_can_be_valid()
    {
        $this->isValid();
    }

}
