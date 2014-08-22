<?php namespace Anomaly\Lexicon\Provider\Laravel;

use Anomaly\Lexicon\Lexicon;
use Anomaly\Lexicon\Node\Block;
use Anomaly\Lexicon\Node\Conditional;
use Anomaly\Lexicon\Node\ConditionalElse;
use Anomaly\Lexicon\Node\ConditionalEndif;
use Anomaly\Lexicon\Node\Variable;
use Anomaly\Lexicon\Node\Insert;
use Anomaly\Lexicon\Node\Section;
use Anomaly\Lexicon\Node\SectionExtends;
use Anomaly\Lexicon\Node\SectionShow;
use Anomaly\Lexicon\Node\SectionStop;
use Anomaly\Lexicon\Node\SectionYield;
use Anomaly\Lexicon\Node\Set;
use Anomaly\Lexicon\Conditional\ConditionalHandler;
use Anomaly\Lexicon\Conditional\Test\IterateableTest;
use Anomaly\Lexicon\Conditional\Test\StringTest;
use Anomaly\Lexicon\Regex;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

/**
 * Class LexiconServiceProvider
 *
 * @package Anomaly\Lexicon\Provider\Laravel
 */
class LexiconServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->package('anomaly/lexicon', null, __DIR__ . '/../../../..');

        $this
            ->registerConditionalHandler()
            ->registerPluginHandler();

        /** @var $app Application */
        $app = $this->app;

        $app->singleton(
            'lexicon',
            function () use ($app) {

                $scopeGlue = $app['config']->get('lexicon::scopeGlue', '.');

                $optimize = $app['config']->get('lexicon::optimize', false);

                $optimizeViewClass = $app['config']->get('lexicon::optimizeViewClass', 'AnomalyLexiconView__');

                $allowPhp = $app['config']->get('lexicon::allowPhp', false);

                $lexicon = new Lexicon(new Regex($scopeGlue), $app['lexicon.conditional.handler'], $app['lexicon.plugin.handler']);

                $lexicon
                    ->setAllowPhp($allowPhp)
                    ->setOptimize($optimize)
                    ->setOptimizeViewClass($optimizeViewClass)
                    ->setIgnoredMatchers(['parent'])
                    ->registerPlugin('foo', 'Anomaly\\Lexicon\\Example\\FooPlugin')
                    ->registerPlugin('test', 'Anomaly\\Lexicon\\Example\\TestPlugin')
                    ->registerPlugin('counter', 'Anomaly\\Lexicon\\Plugin\\Counter')
                    ->registerRootNodeType(new Block($lexicon))
                    ->registerNodeTypes(
                        [
                            new Section($lexicon),
                            new SectionExtends($lexicon),
                            new SectionShow($lexicon),
                            new SectionStop($lexicon),
                            new SectionYield($lexicon),
                            new Set($lexicon),
                            new Insert($lexicon),
                            new Conditional($lexicon),
                            new ConditionalElse($lexicon),
                            new ConditionalEndif($lexicon),
                            new Variable($lexicon),
                        ]
                    );

                return $lexicon;
            }
        );

        $app->singleton(
            'lexicon.compiler',
            function () use ($app) {
                $cachePath = $app['path.storage'] . '/views';
                // The Compiler engine requires an instance of the CompilerInterface, which in
                // this case will be the Blade compiler, so we'll first create the compiler
                // instance to pass into the engine so it can compile the views properly.
                $compiler = new LexiconCompiler($app['files'], $cachePath);
                $compiler->setEnvironment($app['lexicon']);
                return $compiler;
            }
        );

        $app->singleton(
            'lexicon.compiler.engine',
            function () use ($app) {
                $engine = new CompilerEngine($app['lexicon.compiler'], $app['files']);

                $engine->setLexicon($app['lexicon']);

                return $engine;
            }
        );

        $app->resolving(
            'view',
            function ($view) use ($app) {
                /** @var $view Environment */
                $view->share('__lexicon', $app['lexicon']);

                $view->addExtension(
                    $extension = $app['config']->get('lexicon::extension', 'html'),
                    'lexicon',
                    function () use ($app) {
                        return $app['lexicon.compiler.engine'];
                    }
                );
            }
        );

        $this->registerEnvironment();

    }

    /**
     * Register the view environment.
     *
     * @return void
     */
    public function registerEnvironment()
    {
        $app = $this->app;

        $app->singleton(
            'lexicon.environment',
            function () use ($app) {
                // Next we need to grab the engine resolver instance that will be used by the
                // environment. The resolver will be used by an environment to get each of
                // the various engine implementations such as plain PHP or Blade engine.
                return new Environment($app['view.engine.resolver'], $app['view.finder'], $app['events']);
            }
        );

        $app->bind(
            'view',
            function ($app) {

                $env = $app['lexicon.environment'];

                // We will also set the container instance on this view environment since the
                // view composers may be classes registered in the container, which allows
                // for great testable, flexible composers for the application developer.
                $env->setContainer($app);

                $env->share('app', $app);

                return $env;
            }
        );
    }

    /**
     * Register conditional handler
     *
     * @return LexiconServiceProvider
     */
    public function registerConditionalHandler()
    {
        $this->app->singleton(
            'lexicon.conditional.handler',
            function () {
                $conditionalHandler = new ConditionalHandler();
                $conditionalHandler
                    ->registerTestType(new StringTest())
                    ->registerTestType(new IterateableTest());
                return $conditionalHandler;
            }
        );

        return $this;
    }

    /**
     * Register plugin handler
     *
     * @return LexiconServiceProvider
     */
    public function registerPluginHandler()
    {
        $this->app->singleton(
            'lexicon.plugin.handler',
            function () {
                return new PluginHandler();
            }
        );
        return $this;
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('lexicon');
    }

}