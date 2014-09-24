<?php namespace spec\Anomaly\Lexicon\Node;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class NodeValidatorSpec
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Node
 */
class NodeValidatorSpec extends ObjectBehavior
{

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Node\NodeValidator');
    }

}
