<?php namespace Anomaly\Lexicon\Contract\View;

use Anomaly\Lexicon\Lexicon;
use Illuminate\Container\Container;

/**
 * Interface FactoryInterface
 *
 * @package Anomaly\Lexicon\Contract\View
 */
interface FactoryInterface
{

    /**
     * Set container
     *
     * @param Container $container
     * @return mixed
     */
    public function setContainer(Container $container);

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
}