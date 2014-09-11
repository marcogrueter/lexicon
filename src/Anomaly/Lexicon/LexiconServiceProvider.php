<?php namespace Anomaly\Lexicon;

use Anomaly\Lexicon\Contract\ConditionalHandlerInterface;
use Anomaly\Lexicon\View\Compiler\Compiler;
use Anomaly\Lexicon\View\Compiler\CompilerEngine;
use Anomaly\Lexicon\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

/**
 * Class LexiconServiceProvider
 *
 * @codeCoverageIgnore
 * @package Anomaly\Lexicon
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
        /** @var $app Application */
        $app = $this->app;

        $this->package('anomaly/lexicon', null, __DIR__ . '/../..');

        $app->singleton(
            'anomaly.lexicon.conditional.handler',
            function () {
                $conditionalHandler = config(
                    'lexicon::conditionalHandler',
                    'Anomaly\Lexicon\Conditional\ConditionalHandler'
                );
                /** @var ConditionalHandlerInterface $conditionalHandler */
                $conditionalHandler = new $conditionalHandler;
                return $conditionalHandler->registerBooleanTestTypes(config('lexicon::booleanTestTypes'), []);
            }
        );

        $app->singleton(
            'anomaly.lexicon.plugin.handler',
            function () {
                $pluginHandler = config(
                    'lexicon::pluginHandler',
                    'Anomaly\Lexicon\Plugin\PluginHandler'
                );
                return new $pluginHandler;
            }
        );

        $app->singleton(
            'anomaly.lexicon',
            function () use ($app) {

                $lexicon = new Lexicon(
                    $app['anomaly.lexicon.conditional.handler'],
                    $app['anomaly.lexicon.plugin.handler']
                );

                $lexicon
                    ->setScopeGlue(config('lexicon::scopeGlue', '.'))
                    ->setViewNamespace(config('lexicon::viewNamespace', 'Anomaly\Lexicon\View'))
                    ->setViewClassPrefix(config('lexicon::viewClassPrefix', 'LexiconView_'))
                    ->setViewTemplatePath(config('lexicon::viewTemplatePath', __DIR__ . '/../../../resources/ViewTemplate.txt'))
                    ->setAllowPhp(config('lexicon::allowPhp', false))
                    ->setDebug(config('lexicon::debug', false))
                    ->registerPlugins(config('lexicon::plugins', []))
                    ->registerNodeTypes(config('lexicon::nodeTypes', []));

                return $lexicon;
            }
        );

        $app->singleton(
            'anomaly.lexicon.compiler',
            function () use ($app) {
                $cachePath = $app['path.storage'] . '/views';
                // The Compiler engine requires an instance of the CompilerInterface, which in
                // this case will be the Blade compiler, so we'll first create the compiler
                // instance to pass into the engine so it can compile the views properly.
                $compiler = new Compiler($app['files'], $cachePath);
                $compiler->setLexicon($app['anomaly.lexicon']);
                return $compiler;
            }
        );

        $app->singleton(
            'anomaly.lexicon.compiler.engine',
            function () use ($app) {
                return new CompilerEngine($app['anomaly.lexicon.compiler'], $app['files']);
            }
        );

        $app->resolving(
            'view',
            function ($view) use ($app) {
                /** @var Factory $view */
                $view->addExtension(
                    config('lexicon::extension', 'html'),
                    'anomaly.lexicon',
                    function () use ($app) {
                        return $app['anomaly.lexicon.compiler.engine'];
                    }
                );
            }
        );

        $app->singleton(
            'anomaly.lexicon.factory',
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

                $env = $app['anomaly.lexicon.factory'];

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
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('anomaly.lexicon');
    }

}