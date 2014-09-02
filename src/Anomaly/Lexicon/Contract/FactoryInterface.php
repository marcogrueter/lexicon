<?php namespace Anomaly\Lexicon\Contract;

interface FactoryInterface
{
    /**
     * @param        $data
     * @param        $key
     * @param array  $attributes
     * @param string $content
     * @return mixed
     */
    public function variable($data, $key, array $attributes = [], $content = '', $default = null, $expected = Expected::ANY);
}