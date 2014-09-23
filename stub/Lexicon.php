<?php namespace Anomaly\Lexicon\Stub;

use Anomaly\Lexicon\Foundation;
use Anomaly\Lexicon\Support\Container;
use Illuminate\Config\FileLoader;
use Illuminate\Config\Repository;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Session\SessionManager;

/**
 * Class Lexicon
 *
 * @package Anomaly\Lexicon\Stub
 */
class Lexicon
{

    /**
     * @return Foundation
     */
    public static function stub()
    {
        $lexicon = new \Anomaly\Lexicon\Lexicon();

        $container = new Container();

        $filesystem = new Filesystem();

        $container->instance(
            'config',
            new Repository(
                new FileLoader($filesystem, __DIR__ . '/../src/config'),
                'development'
            )
        );

        $container['config']['session.driver'] = 'array';

        $sessionManager = new SessionManager($container);

        $lexicon->registerPlugins([
               'stub' => 'Anomaly\Lexicon\Stub\Plugin\StubPlugin'
            ]);

        $lexicon->addViewFinderNamespace('test', __DIR__ . '/../resources/views');

        return $lexicon->register($container, $filesystem, new Dispatcher(), $sessionManager->driver());
    }

} 