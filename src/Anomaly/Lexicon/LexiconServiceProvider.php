<?php namespace Anomaly\Lexicon;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Illuminate\Container\Container;
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
        $lexicon = new Lexicon($this->app);
        $lexicon->register();

        $this->package('anomaly/lexicon');

        return $lexicon;
    }

    /**
     * Stub for testing with PHPSpec
     *
     * @return LexiconServiceProvider
     */
    public static function stub()
    {
        return new static(new Container());
    }

}