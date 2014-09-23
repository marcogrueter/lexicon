<?php namespace Anomaly\Lexicon;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\Support\Container;
use Anomaly\Lexicon\View\Compiler;
use Anomaly\Lexicon\View\Engine;
use Anomaly\Lexicon\View\Factory;
use Illuminate\Config\FileLoader;
use Illuminate\Config\Repository;
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
     * @var Container
     */
    protected $container;

    /**
     * @param Container        $container
     * @param LexiconInterface $lexicon
     */
    public function __construct(LexiconInterface $lexicon, Container $container) {
        $this->lexicon   = $lexicon;
        $this->container = $container;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return LexiconInterface
     */
    public function getLexicon()
    {
        return $this->lexicon;
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
        if (isset($container['config'])) {
            $container->instance(
                'config',
                new Repository(
                    new FileLoader($this->getFilesystem(), __DIR__ . '/../config'),
                    'development'
                )
            );
        }

        return $container['config'];
    }

    /**
     * Get config
     *
     * @param      $key
     * @param null $value
     * @return mixed
     */
    public function getConfig($key, $value = null)
    {
        return $this->getConfigRepository()->get($key);
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
        $container = $this->getContainer();

        $session = null;

        if (!isset($container['session'])) {
            $container->bindShared(
                'session',
                function ($app) {
                    $session = new SessionManager($app);
                    $session->setDefaultDriver($this->getConfig('session.driver', 'array'));
                    return $session;
                }
            );
        }

        if (!isset($container['session.store'])) {
            $container->instance('session.store', $container['session']->driver());
        }

        return $container['session.store'];
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
     * Register
     *
     * @param Container $container
     * @return Foundation
     */
    public function register()
    {
        $this->getContainer()->instance('anomaly.lexicon', $this->lexicon);
        $this->registerFilesystem();
        $this->registerEvents();
        $this->registerEngineResolver();
        $this->registerEngineResolver();
        $this->registerViewFinder();

        // Once the other components have been registered we're ready to include the
        // view environment and session binder. The session binder will bind onto
        // the "before" application event and add errors into shared view data.
        $this->registerFactory();
        $this->registerSessionBinder();

        return $this;
    }

    /**
     * Register filesystem is it is not in the container
     */
    protected function registerFilesystem()
    {
        $container = $this->getContainer();

        if (!isset($container['files'])) {
            $container->instance('files', new Filesystem());
        }
    }

    /**
     * Register events if it is not in the container
     */
    protected function registerEvents()
    {
        $container = $this->getContainer();

        if (!isset($container['events'])) {
            $container->instance('events', new Dispatcher());
        }
    }

    /**
     * Register engine resolver
     *
     * @param Container $container
     */
    public function registerEngineResolver()
    {
        $this->getContainer()->bindShared(
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
     * @return EngineResolver
     */
    public function getEngineResolver()
    {
        return $this->getContainer()->make('view.engine.resolver');
    }

    /**
     * @return Engine
     */
    public function getLexiconEngine()
    {
        return $this->getContainer()->make('anomaly.lexicon.engine');
    }

    /**
     * @return Compiler
     */
    public function getLexiconCompiler()
    {
        return $this->container['anomaly.lexicon.compiler'];
    }

    /**
     * Register the Lexicon engine implementation.
     *
     * @param  \Illuminate\View\Engines\EngineResolver $resolver
     * @return void
     */
    public function registerLexiconEngine(EngineResolver $resolver)
    {
        $this->getContainer()->bindShared(
            'anomaly.lexicon.compiler',
            function () {
                $compiler = new Compiler($this->getFilesystem(), $this->getLexicon()->getStoragePath());
                $compiler->setLexicon($this->getLexicon());
                return $compiler;
            }
        );

        $this->getContainer()->bindShared(
            'anomaly.lexicon.engine',
            function () {
                return new Engine($this->getLexiconCompiler(), $this->getFilesystem());
            }
        );

        $resolver->register(
            'lexicon',
            function () {
                return $this->getLexiconEngine();
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
        $this->getContainer()->bindShared(
            'blade.compiler',
            function () {
                return new BladeCompiler($this->getFilesystem(), $this->getLexicon()->getStoragePath());
            }
        );

        $resolver->register(
            'blade',
            function () {
                return new CompilerEngine($this->getContainer()->make('blade.compiler'), $this->getFilesystem());
            }
        );
    }

    /**
     * @return Factory
     */
    public function getFactory()
    {
        return $this->getContainer()->make('view');
    }

    /**
     * Register the view finder implementation.
     *
     * @return void
     */
    public function registerViewFinder()
    {
        $this->getContainer()->bindShared(
            'view.finder',
            function () {
                return new FileViewFinder($this->getFilesystem(), $this->getLexicon()->getViewPaths());
            }
        );
    }

    public function registerFactory()
    {
        $this->getContainer()->bindShared(
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

                $factory->addExtension(
                    $this->getLexicon()->getExtension(),
                    'lexicon',
                    function () {
                        return $this->getLexiconEngine();
                    }
                );

                return $factory;
            }
        );
    }

    protected function getSessionDriver()
    {
        $driver = 'array';

        if (isset($container['config']) and
            isset($container['config']['session']) and
            is_string($container['config']['session'])
        ) {
            $driver = $container['config']['session'];
        }

        return $driver;
    }

    /**
     * Register the session binder for the view environment.
     *
     * @return void
     */
    protected function registerSessionBinder()
    {
        $this->getContainer()->booted(
            function () {
                // If the current session has an "errors" variable bound to it, we will share
                // its value with all view instances so the views can easily access errors
                // without having to bind. An empty bag is set when there aren't errors.
                if ($this->sessionHasErrors()) {
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

}
