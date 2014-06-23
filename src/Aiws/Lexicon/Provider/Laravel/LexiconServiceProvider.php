<?php namespace Aiws\Lexicon\Provider\Laravel;

use Aiws\Lexicon\Lexicon;
use Aiws\Lexicon\Node\Block;
use Aiws\Lexicon\Node\Conditional;
use Aiws\Lexicon\Node\ConditionalElse;
use Aiws\Lexicon\Node\ConditionalEnd;
use Aiws\Lexicon\Node\Variable;
use Aiws\Lexicon\Provider\Laravel\Node\Insert;
use Aiws\Lexicon\Provider\Laravel\Node\Section;
use Aiws\Lexicon\Provider\Laravel\Node\SectionExtends;
use Aiws\Lexicon\Provider\Laravel\Node\SectionShow;
use Aiws\Lexicon\Provider\Laravel\Node\SectionStop;
use Aiws\Lexicon\Provider\Laravel\Node\SectionYield;
use Aiws\Lexicon\Util\ConditionalHandler;
use Aiws\Lexicon\Util\Regex;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class LexiconServiceProvider extends ServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
       // dd('hello');

        $this->package('aiws/lexicon', null, __DIR__.'/../../../..');

        /** @var $app Application */
        $app = $this->app;

        $app->singleton('lexicon', function() use ($app) {

                $scopeGlue = $app['config']->get('lexicon::scopeGlue', '.');

                $lexicon = new Lexicon(new Regex($scopeGlue), new ConditionalHandler(), new PluginHandler());

                $lexicon->setIgnoredMatchers(['parent']);

                $lexicon
                    ->registerPlugin('test', 'Aiws\\Lexicon\\Example\\TestPlugin')
                    ->registerRootNodeType(new Block())
                    ->registerNodeTypes(
                        [
                            new Section(),
                            new SectionExtends(),
                            new SectionShow(),
                            new SectionStop(),
                            new SectionYield(),
                            new Insert(),
                            new Conditional(),
                            new ConditionalElse(),
                            new ConditionalEnd(),
                            new Variable(),
                        ]
                    );

                return $lexicon;
            });

        $app->resolving(
            'view',
            function ($view) use ($app) {

                $extension = $app['config']->get('lexicon::extension', 'html');

                /** @var $view Environment */
                $view->share('__lexicon', $app['lexicon']);

                $view->addExtension(
                    $extension,
                    'lexicon',
                    function () use ($app) {
                        $cachePath = $app['path.storage'] . '/views';

                        // The Compiler engine requires an instance of the CompilerInterface, which in
                        // this case will be the Blade compiler, so we'll first create the compiler
                        // instance to pass into the engine so it can compile the views properly.
                        $compiler = new Compiler($app['files'], $cachePath);

                        $compiler->setEnvironment($app['lexicon']);

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
        return array('lexicon');
    }

}