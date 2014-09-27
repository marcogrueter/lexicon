<?php namespace Anomaly\Lexicon\Stub;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\View\CompilerInterface;
use Anomaly\Lexicon\Contract\View\EngineInterface;
use Anomaly\Lexicon\Foundation;
use Anomaly\Lexicon\Lexicon;
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
class LexiconStub
{

    /**
     * @return LexiconInterface
     */
    public static function stub()
    {
        $lexicon = new Lexicon();

        return $lexicon
            ->setStandalone(true)
            ->addNamespace('test', __DIR__ . '/../views')
            ->setStoragePath(__DIR__ . '/../storage/views')
            ->addParsePath('<h1>Hello {{ name }}</h1>')
            ->registerPlugin('stub', 'Anomaly\Lexicon\Plugin\StubPlugin')
            ->registerNodeGroup(
                [
                    'Anomaly\Lexicon\Node\NodeType\Block',
                    'Anomaly\Lexicon\Stub\Node\Undefined'
                ],
                'testing'
            )
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