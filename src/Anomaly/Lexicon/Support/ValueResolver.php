<?php namespace Anomaly\Lexicon\Support;

/**
 * Class ValueResolver
 *
 * @package Anomaly\Lexicon\Support
 */
class ValueResolver
{
    /**
     * @var array
     */
    public $pass = array(
        'null',
        'true',
        'false',
    );

    /**
     * Resolve value
     *
     * @param string $value
     * @return string
     */
    public function resolve($value = '')
    {
        $value = trim($value);

        // this shouldn't happen
        if (is_null($value) or is_array($value)) {

            $value = 'null';

        } elseif (in_array($value, $this->pass)) {

            // pass value as is

        } elseif (preg_match('/^\'(.*)\'$/', $value, $matches)) {

            $value = "'{$matches[1]}'";

        } elseif (preg_match('/^"(.*)"$/', $value, $matches)) {

            $value = $matches[1];

        } elseif (preg_match('/^(\d+)$/', $value, $matches)) {

            $value = $matches[1];

        } elseif (preg_match('/^(\d[\d\.]+)$/', $value, $matches)) {

            $value = $matches[1];

        }

        return $value;
    }

} 