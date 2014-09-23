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

        $lexicon->register();
    }

}