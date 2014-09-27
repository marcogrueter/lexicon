<?php namespace spec\Anomaly\Lexicon;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Support\Container;
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

    function let()
    {
        $this->beConstructedThrough('stub');
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
