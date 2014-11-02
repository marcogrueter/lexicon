<?php namespace Anomaly\Lexicon\Stub;

use Anomaly\Lexicon\LexiconServiceProvider;
use Illuminate\Container\Container;
use Illuminate\Config\FileLoader;
use Illuminate\Config\Repository;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Session\SessionManager;

/**
 * Class LexiconServiceProviderStub
 *
 * @package Anomaly\Lexicon\Stub
 */
class LexiconServiceProviderStub
{
    public static function get()
    {
        return new LexiconServiceProvider(new Container());
    }
} 