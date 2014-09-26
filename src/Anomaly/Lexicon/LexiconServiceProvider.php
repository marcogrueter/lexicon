<?php namespace Anomaly\Lexicon;


use Anomaly\Lexicon\Contract\LexiconInterface;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

/**
 * Class LexiconServiceProvider
 *
 * @package Anomaly\Lexicon
 */
class LexiconServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return LexiconInterface
     */
    public function register()
    {
        $app = $this->app;

        $lexicon = new Lexicon($app);

        // TODO: Revisit this config default
        $lexicon->setExtension('html');

        $lexicon->register();

        return $lexicon;
    }

}