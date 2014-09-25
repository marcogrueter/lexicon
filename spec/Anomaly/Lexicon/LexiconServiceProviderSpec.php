<?php namespace spec\Anomaly\Lexicon;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\Support\ContainerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class LexiconServiceProviderSpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon
 */
class LexiconServiceProviderSpec extends ObjectBehavior
{

    function let(ContainerInterface $application)
    {
        $this->beConstructedWith($application);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\LexiconServiceProvider');
    }

    function it_can_register_lexicon(LexiconInterface $lexicon)
    {
        $this->register()->shouldImplement('Anomaly\Lexicon\Contract\LexiconInterface');
    }
}
