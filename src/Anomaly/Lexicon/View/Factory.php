<?php namespace Anomaly\Lexicon\View;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\Plugin\PluginHandlerInterface;
use Anomaly\Lexicon\Contract\View\EngineInterface;
use Anomaly\Lexicon\Contract\View\FactoryInterface;
use Anomaly\Lexicon\Lexicon;
use Anomaly\Lexicon\Stub\LexiconStub;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory as BaseFactory;

/**
 * Class Factory
 *
 * @package Anomaly\Lexicon\View
 */
class Factory extends BaseFactory implements FactoryInterface
{

    /**
     * @var EngineResolver
     */
    protected $engines;

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string $view
     * @param  array  $data
     * @param  array  $mergeData
     * @return View
     */
    public function parse($view, $data = [], $mergeData = [])
    {
        $this->getLexicon()->addStringTemplate($view);

        return $this->newView($this->getLexiconEngine(), md5($view), $view, $data, $mergeData);
    }

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string $view
     * @param  array  $data
     * @param  array  $mergeData
     * @return View
     */
    public function make($view, $data = [], $mergeData = [])
    {
        if (isset($this->aliases[$view])) {
            $view = $this->aliases[$view];
        }

        $view = $this->normalizeName($view);

        $path = $this->finder->find($view);

        return $this->newView($this->getEngineFromPath($path), $view, $path, $data, $mergeData);
    }

    /**
     * New engine
     *
     * @param $engine
     * @param $view
     * @param $path
     * @param $data
     * @return View
     */
    public function newView($engine, $view, $path, $data = [], $mergeData = [])
    {
        $data = array_merge($mergeData, $this->parseData($data));

        $this->callCreator($view = new View($this, $engine, $view, $path, $data));

        return $view;
    }

    /**
     * @return LexiconInterface
     */
    public function getLexicon()
    {
        return $this->container['anomaly.lexicon'];
    }

    /**
     * EngineInterface
     *
     * @return EngineInterface
     */
    public function getLexiconEngine()
    {
        return $this->engines->resolve('lexicon');
    }

    /**
     * Factory stub for PHPSpec test
     *
     * @return Factory
     */
    public static function stub()
    {
        return LexiconStub::factory();
    }

}
