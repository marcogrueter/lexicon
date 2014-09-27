<?php namespace Anomaly\Lexicon\Stub;

use Anomaly\Lexicon\LexiconServiceProvider;
use Anomaly\Lexicon\Support\Container;
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
    public static function stub()
    {
        $container = new Container();

        $container['path.base'] = __DIR__ . '/../../..';

        $container['session.driver'] = 'array';

        $container->bindShared(
            'events',
            function () {
                return new Dispatcher();
            }
        );

        $container->bindShared(
            'session',
            function ($container) {
                $session = new SessionManager($container);
                $session->setDefaultDriver('array');
                return $session;
            }
        );

        $container->bindShared(
            'session.store',
            function () use ($container) {
                $session = $container['session'];
                /** @var SessionManager $session */
                return $session->driver();
            }
        );

        $container->bindShared(
            'files',
            function () {
                return new Filesystem();
            }
        );

        $container->bindShared(
            'config',
            function () use ($container) {
                return new Repository(
                    new FileLoader($container['files'], __DIR__ . '/../../config'),
                    'testing'
                );
            }
        );

        return new LexiconServiceProvider($container);
    }
} 