<?php namespace Anomaly\Lexicon;

use Anomaly\Lexicon\Conditional\ConditionalHandler;
use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\Node\NodeInterface;
use Anomaly\Lexicon\Node\NodeCollection;
use Anomaly\Lexicon\Node\NodeExtractor;
use Anomaly\Lexicon\Node\NodeFactory;
use Anomaly\Lexicon\Node\NodeFinder;
use Anomaly\Lexicon\Plugin\PluginHandler;
use Anomaly\Lexicon\Stub\LexiconStub;
use Anomaly\Lexicon\View\Compiler;
use Anomaly\Lexicon\View\Engine;
use Anomaly\Lexicon\View\Factory;
use Composer\Autoload\ClassLoader;
use Illuminate\Config\FileLoader;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
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
     * @var LexiconInterface
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
        $this->registerBasePath();
        $this->registerLexicon();
        $this->registerFilesystem();
        $this->registerEvents();
        $this->registerConfigRepository();
        $this->registerNodeFinder();
        $this->registerNodeFactory();
        $this->registerPluginHandler();
        $this->registerConditionalHandler();
        $this->registerEngineResolver();
        $this->registerViewFinder();
        $this->registerFactory();


        return $this;
    }

    /**
     * Register Lexicon
     */
    public function registerLexicon()
    {
        $this->getContainer()->bindShared(
            'anomaly.lexicon',
            function () {
                return $this->lexicon;
            }
        );
    }

    public function registerBasePath()
    {
        $this->getContainer()->bindIf(
            'path.base',
            function () {
                return $this->getLexicon()->getBasePath();
            }
        );
    }

    /**
     * Register the file system
     */
    public function registerFilesystem()
    {
        $this->getContainer()->bindIf(
            'files',
            function () {
                return new Filesystem();
            },
            true
        );
    }

    /**
     * Register events if it is not in the container
     */
    public function registerEvents()
    {
        $this->getContainer()->bindIf(
            'events',
            function () {
                return new Dispatcher();
            },
            true
        );
    }

    /**
     * Register config repository
     */
    public function registerConfigRepository()
    {
        $this->getContainer()->bindIf(
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
            },
            true
        );
    }

    /**
     * Register Lexicon config
     */
    public function registerPluginHandler()
    {
        $this->getContainer()->bindShared(
            'anomaly.lexicon.plugin.handler',
            function () {

                if (!$pluginHandler = $this->getLexicon()->getPluginHandler()) {
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
        $this->getContainer()->bindShared(
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

    public function registerNodeFinder()
    {
        $this->getContainer()->bind(
            'anomaly.lexicon.node.finder',
            function ($container, $parameters) {
                return new NodeFinder($parameters[0]);
            }
        );
    }

    /**
     * Lexicon node groups
     */
    public function registerNodeFactory()
    {
        $this->getContainer()->bindShared(
            'anomaly.lexicon.node.factory',
            function () {

                $nodeFactory = new NodeFactory(
                    $lexicon = $this->getLexicon(),
                    new NodeCollection(),
                    new NodeExtractor($lexicon)
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
     * Register the Lexicon engine implementation.
     *
     * @param  \Illuminate\View\Engines\EngineResolver $resolver
     * @return void
     */
    public function registerLexiconEngine(EngineResolver $resolver)
    {
        $container = $this->getContainer();

        $this->getContainer()->bindShared(
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
                /** @var ClassLoader $loader */
                $loader = new ClassLoader();
                $loader->addPsr4($this->getLexicon()->getCompiledViewNamespace() . '\\', $this->getStoragePath());
                $loader->register();
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

        $container->bindShared(
            'blade.compiler',
            function () {
                return new BladeCompiler($this->getFilesystem(), $this->getStoragePath());
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
        $this->getContainer()->bindShared(
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

                $factory->addExtension($this->getExtension(), 'lexicon');

                return $factory;
            }
        );
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
     * Is debug
     *
     * @return bool
     */
    public function isDebug()
    {
        return $this->lexicon->isDebug() ?: $this->getConfig('app.debug', false);
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
     * Get node finder
     *
     * @param NodeInterface $node
     * @return NodeFinder
     */
    public function getNodeFinder(NodeInterface $node)
    {
        return $this->getContainer()->make('anomaly.lexicon.node.finder', [$node]);
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
        if (!$storagePath = $this->getLexicon()->getStoragePath()) {
            if (!$storagePath = $this->getConfig('lexicon::storagePath')) {
                $storagePath = $this->getConfig('view.compiled');
            }
        }

        return $storagePath;
    }

    /**
     * Get factory
     *
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
        if (!$viewPaths = $this->getLexicon()->getViewPaths()) {
            $viewPaths = $this->getConfig('view.paths', [__DIR__ . '/../../../resources/views']);
        }

        return $viewPaths;
    }

    /**
     * @return mixed|object
     */
    public function getViewFinder()
    {
        return $this->getContainer()->make('view.finder');
    }

    /**
     * Get compiler sequence
     */
    public function getCompilerSequence()
    {
        return $this->getLexicon()->getCompilerSequence() ?: $this->getConfig(
            'lexicon::compilerSequence',
            [
                'Anomaly\Lexicon\View\LexiconCompiler'
            ]
        );
    }

    /**
     * @return static
     */
    public static function stub()
    {
        return new static(LexiconStub::get());
    }

}
