<?php namespace spec\Anomaly\Lexicon\Node;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class SectionStopSpec
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Node
 */
class SectionStopSpec extends ObjectBehavior
{

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Node\SectionStop');
    }

}
