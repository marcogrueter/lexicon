<?php namespace Anomaly\Lexicon\Conditional\Test;


/**
 * Class StringTest
 *
 * @codeCoverageIgnore
 * @package Anomaly\Lexicon\Conditional\Test
 */
class StringTest
{

    /**
     * Contains
     *
     * @param $left
     * @param $right
     * @return bool
     */
    public function contains($haystack, $needle)
    {
        return str_contains($haystack, $needle);
    }

    /**
     * Starts with
     *
     * @param $haystack
     * @param $needles
     * @return bool
     */
    public function startsWith($haystack, $needle)
    {
        return starts_with($haystack, $needle);
    }

    /**
     * Ends with
     *
     * @param $haystack
     * @param $needles
     * @return bool
     */
    public function endsWith($haystack, $needle)
    {
        return ends_with($haystack, $needle);
    }

    /**
     * Is
     *
     * @param $haystack
     * @param $needles
     * @return bool
     */
    public function is($value, $pattern)
    {
        return str_is($value, $pattern);
    }

}