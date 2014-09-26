<?php namespace Anomaly\Lexicon\Contract\View;

use Anomaly\Lexicon\Lexicon;
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

    /**
     * @param        $data
     * @param        $key
     * @param array  $attributes
     * @param string $content
     * @param null   $default
     * @param string $expected
     * @return mixed
     */
    public function variable($data, $key, array $attributes = [], $content = '', $default = null, $expected = Lexicon::EXPECTED_ANY);

    /**
     * Boolean test
     *
     * @param      $left
     * @param null $right
     * @param null $operator
     * @return bool
     */
    public function booleanTest($left, $right, $operator);

    /**
     * Return expected data type as a fallback to wrong data type
     *
     * @param        $data
     * @param string $expected
     * @param null   $finalResult
     * @return array|bool|float|int|null|string|\Traversable
     */
    public function expected($data, $expected = Lexicon::EXPECTED_ANY, $finalResult = null);

}