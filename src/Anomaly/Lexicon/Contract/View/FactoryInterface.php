<?php namespace Anomaly\Lexicon\Contract\View;

use Anomaly\Lexicon\Lexicon;
use Anomaly\Lexicon\View\View;
use Illuminate\Container\Container;
use Illuminate\Contracts\View\Factory;

/**
 * Interface FactoryInterface
 *
 * @package Anomaly\Lexicon\Contract\View
 */
interface FactoryInterface extends Factory
{

    /**
     * Set container
     *
     * @param Container $container
     * @return mixed
     */
    public function setContainer(Container $container);

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string $view
     * @param  array  $data
     * @param  array  $mergeData
     * @return View
     */
    public function parse($view, $data = [], $mergeData = []);

}