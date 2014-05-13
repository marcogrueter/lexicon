<?php namespace Aiws\Lexicon\Adapter\Laravel;

use Illuminate\Support\ServiceProvider;
use Aiws\Lexicon\Adapter\Laravel\Compiler;
use Aiws\Lexicon\Adapter\Laravel\CompilerEngine;
use Aiws\Lexicon\Adapter\Laravel\Environment;

class LexiconServiceProvider extends ServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $app = $this->app;

        $app->resolving(
            'view',
            function ($view) use ($app) {
                $view->addExtension(
                    'lex',
                    'lexicon',
                    function () use ($app) {
                        $cachePath = $app['path.storage'] . '/views';

                        $parserCachePath = $app['path.storage'] . '/lexicon';

                        // The Compiler engine requires an instance of the CompilerInterface, which in
                        // this case will be the Blade compiler, so we'll first create the compiler
                        // instance to pass into the engine so it can compile the views properly.
                        $compiler = new Compiler($app['files'], $cachePath);

                        $compiler->boot($parserCachePath);

                        return new CompilerEngine($compiler, $app['files']);
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

        $app->bind(
            'view',
            function ($app) {
                // Next we need to grab the engine resolver instance that will be used by the
                // environment. The resolver will be used by an environment to get each of
                // the various engine implementations such as plain PHP or Blade engine.
                $resolver = $app['view.engine.resolver'];

                $finder = $app['view.finder'];

                $env = new Environment($resolver, $finder, $app['events']);

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
		return array();
	}

}