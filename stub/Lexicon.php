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

    public static $nodeSets = [
        'all'       => [
            'Anomaly\Lexicon\Stub\Node\Undefined',
            'Anomaly\Lexicon\Node\Comment',
            'Anomaly\Lexicon\Node\IgnoreBlock',
            'Anomaly\Lexicon\Node\IgnoreVariable',
            'Anomaly\Lexicon\Node\Conditional',
            'Anomaly\Lexicon\Node\ConditionalElse',
            'Anomaly\Lexicon\Node\ConditionalEndif',
            'Anomaly\Lexicon\Node\Block',
            'Anomaly\Lexicon\Node\Recursive',
            'Anomaly\Lexicon\Node\Section',
            'Anomaly\Lexicon\Node\SectionAppend',
            'Anomaly\Lexicon\Node\SectionExtends',
            'Anomaly\Lexicon\Node\SectionOverwrite',
            'Anomaly\Lexicon\Node\SectionShow',
            'Anomaly\Lexicon\Node\SectionStop',
            'Anomaly\Lexicon\Node\SectionYield',
            'Anomaly\Lexicon\Node\Includes',
            'Anomaly\Lexicon\Node\Variable',
        ],
        /**
         * Compile without layout features
         */
        'simple'    => [
            'Anomaly\Lexicon\Node\Comment',
            'Anomaly\Lexicon\Node\Conditional',
            'Anomaly\Lexicon\Node\ConditionalElse',
            'Anomaly\Lexicon\Node\ConditionalEndif',
            'Anomaly\Lexicon\Node\IgnoreBlock',
            'Anomaly\Lexicon\Node\IgnoreVariable',
            'Anomaly\Lexicon\Node\Block',
            'Anomaly\Lexicon\Node\Recursive',
            'Anomaly\Lexicon\Node\Variable',
        ],
        /**
         * Compile without Blocks
         */
        'variables' => [
            'Anomaly\Lexicon\Node\Comment',
            'Anomaly\Lexicon\Node\IgnoreVariable',
            'Anomaly\Lexicon\Node\Conditional',
            'Anomaly\Lexicon\Node\ConditionalElse',
            'Anomaly\Lexicon\Node\ConditionalEndif',
            'Anomaly\Lexicon\Node\Variable',
        ]
    ];

    public static $plugins = [
        'stub' => 'Anomaly\Lexicon\Stub\Plugin\StubPlugin'
    ];

    /**
     * @return Foundation
     */
    public static function stub()
    {
        $lexicon = new \Anomaly\Lexicon\Lexicon(new Container());

        $storage = __DIR__ . '/../resources/storage/views';;

        $lexicon
            ->setDebug(true)
            ->setStoragePath($storage)
            ->registerPlugins(static::$plugins)
            ->registerNodeSets(static::$nodeSets)
            ->addParsePath('<h1>Hello {{ name }}</h1>')
            ->addViewFinderNamespace('test', __DIR__ . '/../resources/views');

        return $lexicon->register();
    }

} 