<?php namespace spec\Anomaly\Lexicon;

use Anomaly\Lexicon\Contract\Support\ContainerInterface;
use Anomaly\Lexicon\Lexicon;
use Anomaly\Lexicon\View\Engine;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Container\Container;
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

    function let()
    {
        $this->beConstructedThrough('stub');
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

    function it_can_register_the_lexicon_engine(EngineResolver $resolver, Engine $lexiconEngine)
    {
        $resolver->register('lexicon', Argument::type('closure'))->shouldBeCalled();
        $this->registerLexiconEngine($resolver);
        $this->getEngineResolver()->resolve('lexicon')->shouldHaveType('Anomaly\Lexicon\View\Engine');
    }

    function it_can_register_the_php_engine(EngineResolver $resolver)
    {
        $this->registerPhpEngine($resolver);
        $this->getEngineResolver()->resolve('php')->shouldHaveType('Illuminate\View\Engines\PhpEngine');
    }

    function it_can_register_the_blade_engine(EngineResolver $resolver)
    {
        $this->registerBladeEngine($resolver);
        $this->getEngineResolver()->resolve('blade')->shouldHaveType('Illuminate\View\Engines\CompilerEngine');
    }

    function it_can_get_config_repository()
    {
        $this->getConfigRepository()->shouldHaveType('Illuminate\Config\Repository');
    }

    function it_can_set_and_get_config(Repository $config)
    {
        $this->setConfig('lexicon::nodeGroups', [1, 2 ,3]);
        $this->getConfig('lexicon::nodeGroups')->shouldReturn([1, 2 ,3]);
    }

    function it_can_get_session_driver()
    {
        $this->getSessionDriver()->shouldReturn('array');
    }

    function it_can_get_session_store()
    {
        $this->getSessionStore();
    }

    function it_can_check_if_session_has_errors()
    {
        $this->sessionHasErrors()->shouldBeBoolean();
    }

    function it_can_register_session_binder(Container $container)
    {
        $this->registerSessionBinder($container);
    }

    function it_can_register_session_binder_when_session_has_errors(Container $container)
    {
        $this->registerSessionBinder($container, true);
    }

    function it_can_get_view_paths()
    {
        $this->getLexicon()->setViewPaths(['foo']);
        $this->setConfig('view.paths', ['foo']);
        $this->getViewPaths()->shouldBeArray();
    }
    
}
