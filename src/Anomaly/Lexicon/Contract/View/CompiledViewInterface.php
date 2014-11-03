<?php namespace Anomaly\Lexicon\Contract\View;

use Anomaly\Lexicon\Lexicon;

/**
 * Interface CompiledViewInterface
 *
 * @package Anomaly\Lexicon\Contract\View
 */
interface CompiledViewInterface
{
    /**
     * Renders the view contents
     *
     * @param $__data
     * @return void
     */
    public function render($__data);

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

}