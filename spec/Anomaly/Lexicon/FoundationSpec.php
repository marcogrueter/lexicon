<?php namespace spec\Anomaly\Lexicon;

use Anomaly\Lexicon\Contract\Support\ContainerInterface;
use Anomaly\Lexicon\Lexicon;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Session\SessionInterface;
use Illuminate\View\Engines\EngineResolver;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class FoundationSpec
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package spec\Anomaly\Lexicon
 */
class FoundationSpec extends ObjectBehavior
{

    function let(Lexicon $lexicon, ContainerInterface $container)
    {
        $this->beConstructedWith($lexicon, $container);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Anomaly\Lexicon\Foundation');
    }

    function it_can_get_lexicon()
    {
        $this->getLexicon()->shouldImplement('Anomaly\Lexicon\Contract\LexiconInterface');
    }

    function it_can_register_lexicon(ContainerInterface $container)
    {
        $this->register($container)->shouldHaveType('Anomaly\Lexicon\Foundation');
    }

    function it_can_register_the_engine_resolver(ContainerInterface $container)
    {
        $this->registerEngineResolver($container);
    }

    function it_can_register_the_lexicon_engine(EngineResolver $resolver)
    {
        $this->registerLexiconEngine($resolver);
    }

    function it_can_register_the_php_engine(EngineResolver $resolver)
    {
        $this->registerPhpEngine($resolver);
    }
}
