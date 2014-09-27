<?php namespace Anomaly\Lexicon;

use Anomaly\Lexicon\Conditional\ConditionalHandler;
use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\Plugin\PluginHandlerInterface;
use Anomaly\Lexicon\Node\NodeCollection;
use Anomaly\Lexicon\Node\NodeExtractor;
use Anomaly\Lexicon\Node\NodeFactory;
use Anomaly\Lexicon\Node\NodeFinder;
use Anomaly\Lexicon\Plugin\PluginHandler;
use Anomaly\Lexicon\Stub\LexiconStub;
use Anomaly\Lexicon\View\Compiler;
use Anomaly\Lexicon\View\Engine;
use Anomaly\Lexicon\View\Factory;
use Illuminate\Config\FileLoader;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Session\SessionInterface;
use Illuminate\Session\SessionManager;
use Illuminate\Support\ViewErrorBag;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\FileViewFinder;

/**
 * Class Foundation
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package Anomaly\Lexicon
 */
class Foundation
{

    /**
     * Engines
     *
     * @var array
     */
    protected $engines = ['lexicon', 'php', 'blade'];

    /**
     * @var Container
     */
    protected $lexicon;

    /**
     * @param LexiconInterface $lexicon
     */
    public function __construct(LexiconInterface $lexicon)
    {
        $this->lexicon = $lexicon;
    }

    /**
     * Register
     *
     * @param Container $container
     * @return Foundation
     */
    public function register()
    {
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

        return $this;
    }

    public function registerLexicon()
    {
        $this->bindShared(
            'anomaly.lexicon',
            function () {
                return $this->lexicon;
            }
        );
    }

    /**
     * Register the file system
     */
    public function registerFilesystem()
    {
        if ($this->isStandalone()) {
            $this->bindShared(
                'files',
                function () {
                    return new Filesystem();
                }
            );
        }
    }

    /**
     * Register events if it is not in the container
     */
    public function registerEvents()
    {
        if ($this->isStandalone()) {
            $this->bindShared(
                'events',
                function () {
                    return new Dispatcher();
                }
            );
        }
    }

    /**
     * Register config repository
     */
    public function registerConfigRepository()
    {
        if ($this->isStandalone()) {
            $this->bindShared(
                'config',
                function () {
                    $config = new Repository(
                        new FileLoader(
                            $this->getFilesystem(),
                            __DIR__ . '/../../config'
                        ),
                        'development' // TODO: Get this from Lexicon
                    );

                    $config->package('anomaly/lexicon', __DIR__ . '/../../config');

                    return $config;
                }
            );
        }
    }

    /**
     * Register Lexicon config
     */
    public function registerPluginHandler()
    {
        $this->bindShared(
            'anomaly.lexicon.plugin.handler',
            function () {

                $pluginHandler = $this->getLexicon()->getPluginHandler();

                if (!($pluginHandler instanceof PluginHandlerInterface)) {
                    $pluginHandler = new PluginHandler();
                }

                $pluginHandler->setLexicon($this->getLexicon());

                $plugins = array_merge(
                    $this->getLexicon()->getPlugins(),
                    $this->getConfig('lexicon::plugins', [])
                );

                return $pluginHandler->registerPlugins($plugins);
            }
        );
    }

    /**
     * Register boolean test types
     */
    public function registerConditionalHandler()
    {
        $this->bindShared(
            'anomaly.lexicon.conditional.handler',
            function () {
                $conditionalHandler = $this->getLexicon()->getConditionalHandler();
                $booleanTestsTypes  = $this->getLexicon()->getBooleanTestTypes();

                if (!$conditionalHandler) {
                    $conditionalHandler = new ConditionalHandler();
                }

                if (empty($booleanTestsTypes)) {
                    $booleanTestsTypes = $this->getConfig('lexicon::booleanTestTypes', []);
                }

                return $conditionalHandler->registerBooleanTestTypes($booleanTestsTypes);
            }
        );
    }

    /**
     * Lexicon node groups
     */
    public function registerNodeFactory()
    {
        $this->getContainer()->singleton(
            'anomaly.lexicon.node.factory',
            function () {

                $nodeFactory = new NodeFactory(
                    $this->getLexicon(),
                    new NodeCollection(),
                    new NodeExtractor(),
                    new NodeFinder()
                );

                $nodeGroups = array_merge(
                    $this->getLexicon()->getNodeGroups(),
                    $this->getConfig('lexicon::nodeGroups', [])
                );

                return $nodeFactory->registerNodeGroups($nodeGroups);
            }
        );

    }

    /**
     * Register engine resolver
     *
     * @param Container $container
     */
    public function registerEngineResolver()
    {
        $this->bindShared(
            'view.engine.resolver',
            function () {
                $resolver = new EngineResolver();

                // Next we will register the various engines with the resolver so that the
                // environment can resolve the engines it needs for various views based
                // on the extension of view files. We call a method for each engines.
                foreach ($this->engines as $engine) {
                    $this->{'register' . ucfirst($engine) . 'Engine'}($resolver);
                }

                return $resolver;
            }
        );
    }

    /**
     * Register the Lexicon engine implementation.
     *
     * @param  \Illuminate\View\Engines\EngineResolver $resolver
     * @return void
     */
    public function registerLexiconEngine(EngineResolver $resolver)
    {
        $container = $this->getContainer();

        $this->bindShared(
            'anomaly.lexicon.compiler',
            function () {
                $compiler = new Compiler($this->getFilesystem(), $this->getStoragePath());
                $compiler->setLexicon($this->getLexicon());
                return $compiler;
            }
        );

        $resolver->register(
            'lexicon',
            function () use ($container) {
                return new Engine($container['anomaly.lexicon.compiler'], $this->getFilesystem());
            }
        );
    }

    /**
     * Register the PHP engine implementation.
     *
     * @param  \Illuminate\View\Engines\EngineResolver $resolver
     * @return void
     */
    public function registerPhpEngine(EngineResolver $resolver)
    {
        $resolver->register(
            'php',
            function () {
                return new PhpEngine();
            }
        );
    }

    /**
     * Register the Blade engine implementation.
     *
     * @param  \Illuminate\View\Engines\EngineResolver $resolver
     * @return void
     */
    public function registerBladeEngine(EngineResolver $resolver)
    {
        $container = $this->getContainer();

        $this->bindShared(
            'blade.compiler',
            function () {
                return new BladeCompiler($this->getFilesystem(), $this->getLexicon()->getStoragePath());
            }
        );

        $resolver->register(
            'blade',
            function () use ($container) {
                return new CompilerEngine($container['blade.compiler'], $this->getFilesystem());
            }
        );
    }

    /**
     * Register the view finder implementation.
     *
     * @return void
     */
    public function registerViewFinder()
    {
        $this->bindShared(
            'view.finder',
            function () {
                return new FileViewFinder($this->getFilesystem(), $this->getViewPaths());
            }
        );
    }

    /**
     * Register factory
     *
     * @return void
     */
    public function registerFactory()
    {
        $this->bindShared(
            'view',
            function () {
                // Next we need to grab the engine resolver instance that will be used by the
                // environment. The resolver will be used by an environment to get each of
                // the various engine implementations such as plain PHP or Blade engine.
                $factory = new Factory(
                    $this->getEngineResolver(),
                    $this->getViewFinder(),
                    $this->getEventDispatcher()
                );

                // We will also set the container instance on this view environment since the
                // view composers may be classes registered in the container, which allows
                // for great testable, flexible composers for the application developer.
                $factory->setContainer($this->getContainer());

                $factory->share('app', $this->getContainer());

                foreach ($this->getLexicon()->getNamespaces() as $namespace => $hint) {
                    $factory->addNamespace($namespace, $hint);
                }

                $factory->addExtension($this->getExtension(), 'lexicon');

                return $factory;
            }
        );
    }

    /**
     * Register session store
     *
     * @param Container $container
     */
    public function registerSessionStore()
    {
        $container = $this->getContainer();

        if ($this->isStandalone()) {

            $this->getConfigRepository()->set('session.driver', 'array');

            $this->bindShared(
                'session',
                function ($container) {
                    $session = new SessionManager($container);
                    $session->setDefaultDriver('array');
                    return $session;
                }
            );

            $this->bindShared(
                'session.store',
                function () use ($container) {
                    $session = $container['session'];
                    /** @var SessionManager $session */
                    return $session->driver();
                }
            );
        }
    }

    /**
     * Register the session binder for the view environment.
     *
     * @return void
     */
    public function registerSessionBinder($sessionHasErrors = false)
    {
        $container = $this->getContainer();

        if (method_exists($container, 'booted')) {
            $container->booted(
                function () use ($sessionHasErrors) {
                    // If the current session has an "errors" variable bound to it, we will share
                    // its value with all view instances so the views can easily access errors
                    // without having to bind. An empty bag is set when there aren't errors.
                    if ($sessionHasErrors) {
                        $this->getFactory()->share('errors', $this->getSessionStore()->get('errors'));
                    }

                    // Putting the errors in the view for every view allows the developer to just
                    // assume that some errors are always available, which is convenient since
                    // they don't have to continually run checks for the presence of errors.
                    else {
                        $this->getFactory()->share('errors', new ViewErrorBag());
                    }
                }
            );
        }
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->getLexicon()->getContainer();
    }

    /**
     * @return LexiconInterface
     */
    public function getLexicon()
    {
        return $this->lexicon;
    }

    /**
     * @return bool
     */
    public function isStandalone()
    {
        return $this->getLexicon()->isStandalone();
    }

    /**
     * Is debug
     *
     * @return bool
     */
    public function isDebug()
    {
        return $this->lexicon->isDebug() ?: $this->getConfig('lexicon::debug', false);
    }

    /**
     * Get extension
     *
     * @return string
     */
    public function getExtension()
    {
        $extension = $this->getConfig('lexicon::extension', 'html');

        if ($override = $this->getLexicon()->getExtension()) {
            $extension = $override;
        }

        return $extension;
    }

    /**
     * Get conditional handler
     *
     * @return ConditionalHandler
     */
    public function getConditionalHandler()
    {
        return $this->getContainer()->make('anomaly.lexicon.conditional.handler');
    }

    /**
     * Get plugin handler
     *
     * @return Contract\Plugin\PluginHandlerInterface|null
     */
    public function getPluginHandler()
    {
        return $this->getContainer()->make('anomaly.lexicon.plugin.handler');
    }

    /**
     * Get node factory
     *
     * @return NodeFactory
     */
    public function getNodeFactory()
    {
        return $this->getContainer()->make('anomaly.lexicon.node.factory');
    }

    /**
     * Get filesystem
     *
     * @return mixed|object
     */
    public function getFilesystem()
    {
        return $this->getContainer()->make('files');
    }

    /**
     * @return Repository
     */
    public function getConfigRepository()
    {
        return $this->getContainer()->make('config');
    }

    /**
     * Get config
     *
     * @param      $key
     * @param null $value
     * @return mixed
     */
    public function getConfig($key, $default = null)
    {
        return $this->getConfigRepository()->get($key, $default);
    }

    /**
     * Set config
     *
     * @param      $key
     * @param null $value
     * @return $this
     */
    public function setConfig($key, $value = null)
    {
        $this->getConfigRepository()->set($key, $value);
        return $this;
    }

    /**
     * @return mixed|null|object
     */
    public function getSessionStore()
    {
        return $this->getContainer()->make('session.store');
    }

    /**
     * Get event dispatcher
     *
     * @return Dispatcher
     */
    public function getEventDispatcher()
    {
        return $this->getContainer()->make('events');
    }


    /**
     * @return EngineResolver
     */
    public function getEngineResolver()
    {
        return $this->getContainer()->make('view.engine.resolver');
    }

    /**
     * Get storage path
     *
     * @return string
     */
    public function getStoragePath()
    {
        $container = $this->getContainer();

        $storagePath = __DIR__ . '/../../../resources/storage/views';

        if (!$this->isStandalone() and $container['path.storage']) {
            $storagePath = $container['path.storage'] . '/views';
        }

        if ($override = $this->getLexicon()->getStoragePath()) {
            $storagePath = $override;
        }

        return $storagePath;
    }

    /**
     * @return Factory
     */
    public function getFactory()
    {
        return $this->getContainer()->make('view');
    }

    /**
     * Get view paths
     *
     * @return array
     */
    public function getViewPaths()
    {
        $viewPaths = [__DIR__ . '/../../../resources/views'];

        if ($configViewPaths = $this->getConfig('view.paths')) {
            $viewPaths = $configViewPaths;
        }

        if ($override = $this->getLexicon()->getViewPaths()) {
            $viewPaths = $override;
        }

        return $viewPaths;
    }


    /**
     * Get session driver
     *
     * @return mixed
     */
    public function getSessionDriver()
    {
        return $this->getConfig('session.driver', 'array');
    }

    /**
     * Determine if the application session has errors.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return bool
     */
    public function sessionHasErrors()
    {
        return ($store = $this->getSessionStore() and $store->has('errors'));
    }

    /**
     * @return mixed|object
     */
    public function getViewFinder()
    {
        return $this->getContainer()->make('view.finder');
    }

    /**
     * Wrap a Closure such that it is shared.
     *
     * @param  \Closure $closure
     * @return \Closure
     */
    public function share(\Closure $closure)
    {
        return function ($container) use ($closure) {
            // We'll simply declare a static variable within the Closures and if it has
            // not been set we will execute the given Closures to resolve this value
            // and return it back to these consumers of the method as an instance.
            static $object;

            if (is_null($object)) {
                $object = $closure($container);
            }

            return $object;
        };
    }

    /**
     * Bind a shared Closure into the container.
     *
     * @param  string   $abstract
     * @param  \Closure $closure
     * @return void
     */
    public function bindShared($abstract, \Closure $closure)
    {
        $this->getContainer()->bind($abstract, $this->share($closure), true);
    }


    /**
     * @return static
     */
    public static function stub()
    {
        return new static(LexiconStub::stub());
    }

}
