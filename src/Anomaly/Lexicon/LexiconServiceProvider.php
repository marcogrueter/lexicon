<?php namespace Anomaly\Lexicon;

use Anomaly\Lexicon\Conditional\ConditionalHandler;
use Anomaly\Lexicon\Conditional\Test\IterateableTest;
use Anomaly\Lexicon\Conditional\Test\StringTest;
use Anomaly\Lexicon\Plugin\PluginHandler;
use Anomaly\Lexicon\View\Compiler\Compiler;
use Anomaly\Lexicon\View\Compiler\CompilerEngine;
use Anomaly\Lexicon\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

/**
 * Class LexiconServiceProvider
 *
 * @package Anomaly\Lexicon\Provider\Laravel
 */
class LexiconServiceProvider extends ServiceProvider
{

    protected $nodeTypes = [
        'Anomaly\Lexicon\Node\Comment',
        'Anomaly\Lexicon\Node\Parents',
        'Anomaly\Lexicon\Node\Block',
        'Anomaly\Lexicon\Node\Recursive',
        'Anomaly\Lexicon\Node\Section',
        'Anomaly\Lexicon\Node\SectionExtends',
        'Anomaly\Lexicon\Node\SectionShow',
        'Anomaly\Lexicon\Node\SectionStop',
        'Anomaly\Lexicon\Node\SectionYield',
        'Anomaly\Lexicon\Node\Includes',
        'Anomaly\Lexicon\Node\Conditional',
        'Anomaly\Lexicon\Node\ConditionalElse',
        'Anomaly\Lexicon\Node\ConditionalEndif',
        'Anomaly\Lexicon\Node\Variable',
    ];

    protected $plugins = [
        'counter' => 'Anomaly\Lexicon\Plugin\CounterPlugin',
        'foo'     => 'Anomaly\Lexicon\Plugin\FooPlugin',
        'test'    => 'Anomaly\Lexicon\Plugin\TestPlugin',
    ];

    protected $pluginHandler = '';

    protected $conditionalHandler = '';

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->package('anomaly/lexicon', null, __DIR__ . '/../..');

        $this
            ->registerConditionalHandler()
            ->registerPluginHandler();

        /** @var $app Application */
        $app = $this->app;

        $app->singleton(
            'lexicon',
            function () use ($app) {

                $scopeGlue = $app['config']->get('lexicon::scopeGlue', '.');

                $debug = $app['config']->get('lexicon::debug', true);

                $lexicon = new Lexicon(
                    new Regex($scopeGlue),
                    $app['lexicon.conditional.handler'],
                    $app['lexicon.plugin.handler']
                );

                $lexicon
                    ->setAllowPhp(false)
                    ->setOptimize(true)
                    ->setDebug($debug)
                    ->setIgnoredMatchers(['parent'])
                    ->setScopeGlue($scopeGlue)
                    ->registerPlugins($this->plugins)
                    ->registerNodeTypes($this->nodeTypes);

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
                $compiler = new Compiler($app['files'], $cachePath);
                $compiler->setLexicon($app['lexicon']);
                return $compiler;
            }
        );

        $app->singleton(
            'lexicon.compiler.engine',
            function () use ($app) {
                return new CompilerEngine($app['lexicon.compiler'], $app['files']);
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

        $this->registerFactory();

    }

    /**
     * Register the view environment.
     *
     * @return void
     */
    public function registerFactory()
    {
        $app = $this->app;

        $app->singleton(
            'lexicon.environment',
            function () use ($app) {
                // Next we need to grab the engine resolver instance that will be used by the
                // environment. The resolver will be used by an environment to get each of
                // the various engine implementations such as plain PHP or Blade engine.
                return new Factory($app['view.engine.resolver'], $app['view.finder'], $app['events']);
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