<?php namespace Anomaly\Lexicon;


use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Stub\LexiconServiceProviderStub;
use Anomaly\Lexicon\Support\Container;
use Illuminate\Config\FileLoader;
use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
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

        $this->package('anomaly/lexicon');

        $lexicon = new Lexicon($app);
        $lexicon->register();

        return $lexicon;
    }

    /**
     * Stub for testing with PHPSpec
     *
     * @return LexiconServiceProvider
     */
    public static function stub()
    {
        return LexiconServiceProviderStub::stub();
    }

}