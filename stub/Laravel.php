<?php namespace Anomaly\Lexicon\Stub;

use Anomaly\Lexicon\Conditional\ConditionalHandler;
use Anomaly\Lexicon\Lexicon;
use Anomaly\Lexicon\Plugin\PluginHandler;
use Anomaly\Lexicon\View\Compiler;
use Anomaly\Lexicon\View\Engine;
use Anomaly\Lexicon\View\Factory;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\FileViewFinder;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class Laravel
{

    protected $app;

    public function __construct()
    {
        $this->app = $this->register(new Container());
    }

    public function register($app)
    {
        $app->bindShared(
            'files',
            function () {
                return new Filesystem();
            }
        );

        $app->bindShared(
            'filesystem',
            function () use ($app) {
                return new FilesystemManager($app);
            }
        );

        $app->bindShared(
            'filesystem.disk',
            function () use ($app) {
                return $app['filesystem']->disk('local');
            }
        );


        $app->singleton(
            'anomaly.lexicon.conditional.handler',
            function () {
                $conditionalHandler = new ConditionalHandler();
                return $conditionalHandler->registerBooleanTestTypes([]);
            }
        );

        $app->singleton(
            'anomaly.lexicon.plugin.handler',
            function () {
                return new PluginHandler();
            }
        );

        $app->singleton(
            'anomaly.lexicon',
            function () use ($app) {

                $lexicon = new Lexicon(
                    $app['anomaly.lexicon.conditional.handler'],
                    $app['anomaly.lexicon.plugin.handler']
                );

                $lexicon->getPluginHandler()->setLexicon($lexicon);

                $lexicon
                    ->setScopeGlue('.')
                    ->setViewNamespace('Anomaly\Lexicon\View')
                    ->setViewClassPrefix('LexiconView_')
                    ->setViewTemplatePath(
                        $this->resources('ViewTemplate.txt')
                    )
                    ->setAllowPhp(false)
                    ->setDebug(true)
                    ->registerPlugins([
                            'stub' => 'Anomaly\Lexicon\Stub\Plugin\StubPlugin',
                        ])
                    ->registerNodeSets([]);

                return $lexicon;
            }
        );

        $app['events'] = $app->share(
            function ($app) {
                return new Dispatcher($app);
            }
        );

        // The Compiler engine requires an instance of the CompilerInterface, which in
        // this case will be the Blade compiler, so we'll first create the compiler
        // instance to pass into the engine so it can compile the views properly.
        $app->bindShared(
            'anomaly.lexicon.compiler',
            function ($app) {
                $cachePath = $this->resources('storage/views');
                // The Compiler engine requires an instance of the CompilerInterface, which in
                // this case will be the Blade compiler, so we'll first create the compiler
                // instance to pass into the engine so it can compile the views properly.
                $compiler = new Compiler($app['files'], $cachePath);
                $compiler->setLexicon($app['anomaly.lexicon']);
                return $compiler;
            }
        );


        $app->bindShared(
            'view.engine.resolver',
            function ($app) {
                $resolver = new EngineResolver();

                $resolver->register(
                    'anomaly.lexicon',
                    function () use ($app) {
                        return new Engine($app['anomaly.lexicon.compiler']);
                    }
                );

                return $resolver;
            }
        );

        $app->bindShared(
            'view.finder',
            function ($app) {
                $paths = [''];

                return new FileViewFinder($app['files'], $paths);
            }
        );

        $app->singleton(
            'anomaly.lexicon.engine',
            function () use ($app) {
                return new Engine($app['anomaly.lexicon.compiler'], $app['files']);
            }
        );

        $app->bindShared(
            'view',
            function ($app) {
                // Next we need to grab the engine resolver instance that will be used by the
                // environment. The resolver will be used by an environment to get each of
                // the various engine implementations such as plain PHP or Blade engine.
                $resolver = $app['view.engine.resolver'];

                $finder = $app['view.finder'];

                $env = new Factory($resolver, $finder, $app['events']);

                // We will also set the container instance on this view environment since the
                // view composers may be classes registered in the container, which allows
                // for great testable, flexible composers for the application developer.
                $env->setContainer($app);

                $env->share('app', $app);

                $env->addExtension(
                    'html',
                    'anomaly.lexicon',
                    function () use ($app) {
                        return $app['anomaly.lexicon.engine'];
                    }
                );

                return $env;
            }
        );

        return $app;
    }

    public function factory()
    {
        $factory = $this->app['view'];
        $factory->addNamespace('test', $this->resources('views'));
        return $factory;
    }

    public function resources($path)
    {
        return __DIR__ . '/../resources/' . $path;
    }

}