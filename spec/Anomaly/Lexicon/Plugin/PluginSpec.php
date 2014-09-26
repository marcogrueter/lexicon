<?php namespace spec\Anomaly\Lexicon\Plugin;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class PluginSpec
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon\Plugin
 */
class PluginSpec extends ObjectBehavior
{

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Plugin\Plugin');
    }

}
