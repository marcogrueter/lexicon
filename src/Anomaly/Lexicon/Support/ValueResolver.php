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
        // this shouldn't happen
        if (is_array($value) or is_null($value) or is_object($value)) {
            return 'null';
        }

        $value = trim($value);

        if (in_array($value, $this->pass)) {
            return $value;
        }

        if (preg_match('/^\'(.*)\'$/', $value, $matches)) {

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