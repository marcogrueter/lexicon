<?php namespace spec\Anomaly\Lexicon\Support;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class ContainerSpec
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Support
 */
class ContainerSpec extends ObjectBehavior
{

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Support\Container');
    }

    function it_can_call_unused_methods_required_by_the_interface()
    {
        $this->environment();
        $this->isDownForMaintenance();
        $this->register(null);
        $this->registerDeferredProvider(null);
        $this->booting(null);
    }

    function it_can_boot()
    {
        $this->boot();
    }
    
    function it_can_run_booted_callbacks()
    {
        $closure = function() {
            return 'hello';
        };

        $this->booted($closure);

        $this->fireAppCallbacks([$closure]);
    }
}
