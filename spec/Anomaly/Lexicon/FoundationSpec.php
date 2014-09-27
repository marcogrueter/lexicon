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

    function it_can_register_dependencies()
    {
        $container = $this->getContainer();

        $this->registerLexicon();
        $this->registerFilesystem();
        $this->registerEvents();
        $this->registerConfigRepository();
        $this->registerNodeFactory();
        $this->registerPluginHandler();
        $this->registerConditionalHandler();
        $this->registerEngineResolver();
        $this->registerViewFinder();
        $this->registerFactory();
        $this->registerSessionStore();
        $this->registerSessionBinder($this->sessionHasErrors());
        $this->register($container)->shouldHaveType('Anomaly\Lexicon\Foundation');
        $container
            ->make('anomaly.lexicon')
            ->shouldHaveType('Anomaly\Lexicon\Lexicon');
        $resolver = $this->getEngineResolver();

        $resolver->shouldHaveType('Illuminate\View\Engines\EngineResolver');
        $resolver->resolve('lexicon');
        $resolver->resolve('php')->shouldHaveType('Illuminate\View\Engines\PhpEngine');
        $resolver->resolve('blade')->shouldHaveType('Illuminate\View\Engines\CompilerEngine');


    }

    function it_can_be_in_debug_mode()
    {
        $this->isDebug()->shouldBeBoolean();
    }

    function it_can_get_conditional_handler()
    {
        $this->getConditionalHandler()
            ->shouldImplement('Anomaly\Lexicon\Contract\Conditional\ConditionalHandlerInterface');
    }

    function it_can_get_plugin_handler()
    {
        $this->getPluginHandler()->shouldImplement('Anomaly\Lexicon\Contract\Plugin\PluginHandlerInterface');
    }

    function it_can_get_config_repository()
    {
        $this->getConfigRepository()->shouldHaveType('Illuminate\Config\Repository');
    }

    function it_can_get_node_factory()
    {
        $this->getNodeFactory()->shouldHaveType('Anomaly\Lexicon\Node\NodeFactory');
    }

    function it_can_get_view_factory()
    {
        $this->getFactory()->shouldHaveType('Anomaly\Lexicon\View\Factory');
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

    function it_can_get_storage_path()
    {
        $container = $this->getContainer();
        $container['path.storage'] = '../some/folder';
        $this->getLexicon()->setStandalone(false);
        $this->getStoragePath()->shouldBeString();
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
