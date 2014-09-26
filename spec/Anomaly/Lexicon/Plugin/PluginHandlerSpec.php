<?php namespace spec\Anomaly\Lexicon\Plugin;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class PluginHandlerSpec
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Plugin
 */
class PluginHandlerSpec extends ObjectBehavior
{

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Plugin\PluginHandler');
    }

}
