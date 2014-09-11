<?php namespace Anomaly\Lexicon\Contract\View;

interface FactoryInterface
{
    /**
     * @param        $data
     * @param        $key
     * @param array  $attributes
     * @param string $content
     * @param null   $default
     * @param string $expected
     * @return mixed
     */
    public function variable($data, $key, array $attributes = [], $content = '', $default = null, $expected = 'any');
}