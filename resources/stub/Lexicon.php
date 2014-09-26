<?php namespace Anomaly\Lexicon\Stub;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\View\CompilerInterface;
use Anomaly\Lexicon\Contract\View\EngineInterface;
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

    public static $nodeGroups = [
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
     * @return LexiconInterface
     */
    public static function stub(Container $container = null)
    {
        $lexicon = new \Anomaly\Lexicon\Lexicon($container);
        
        return $lexicon
            ->addNamespace('test', __DIR__ . '/../views')
            ->setStoragePath(__DIR__ . '/../storage/views')
            ->registerPlugins(static::$plugins)
            ->registerNodeGroups(static::$nodeGroups)
            ->addParsePath('<h1>Hello {{ name }}</h1>')
            ->setDebug(true)
            ->register();
    }

    /**
     * @return Foundation
     */
    public static function foundation()
    {
        return static::stub()->getFoundation();
    }

    /**
     * @return \Anomaly\Lexicon\View\Factory
     */
    public static function factory()
    {
        return static::foundation()->getFactory();
    }

    /**
     * @return \Illuminate\View\Engines\EngineResolver
     */
    public static function engineResolver()
    {
        return static::foundation()->getEngineResolver();
    }

    /**
     * @return EngineInterface
     */
    public static function engine()
    {
        return static::engineResolver()->resolve('lexicon');
    }

    /**
     * @return CompilerInterface
     */
    public static function compiler()
    {
        return static::engine()->getCompiler();
    }
} 