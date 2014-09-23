<?php namespace Anomaly\Lexicon;


use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

/**
 * Class LexiconServiceProvider
 *
 * @codeCoverageIgnoreStart
 * @package Anomaly\Lexicon
 */
class LexiconServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @codeCoverageIgnore
     * @return void
     */
    public function register()
    {
        $app = $this->app;

        $lexicon = new Lexicon($app);

        $lexicon->setExtension('html');

        $lexicon->setViewPaths($app['config']['view.paths']);

        $session = null;

        $config = $app['config']['session'];

        if (isset($app['session.store']) && ! is_null($config['driver']))
        {
            $session = $app['session.store'];
        }

        $lexicon->register($app, $app['files'], $this->app['events'], $session);
    }

}