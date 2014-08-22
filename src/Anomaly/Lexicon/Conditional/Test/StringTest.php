<?php namespace Anomaly\Lexicon\Conditional\Test;

class StringTest extends TestType
{

    protected $type = 'string';

    /**
     * Contains
     *
     * @param $left
     * @param $right
     * @return bool
     */
    public function contains($haystack, $needle)
    {
        if (!is_string($haystack) or !is_string($needle)) {
            return false;
        }

        if ($needle != '' && strpos($haystack, $needle) !== false) {
            return true;
        }

        return false;
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
        if (!is_string($haystack) or !is_string($needle)) {
            return false;
        }

        if ($needle != '' && strpos($haystack, $needle) === 0) {
            return true;
        }

        return false;
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
        if (!is_string($haystack) or !is_string($needle)) {
            return false;
        }

        if ($needle == substr($haystack, -strlen($needle))) {
            return true;
        }

        return false;
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
        if (!is_string($value) or !is_string($pattern)) {
            return false;
        }

        if ($pattern == $value) {
            return true;
        }

        $pattern = preg_quote($pattern, '#');

        // Asterisks are translated into zero-or-more regular expression wildcards
        // to make it convenient to check if the strings starts with the given
        // pattern such as "library/*", making any string check convenient.
        $pattern = str_replace('\*', '.*', $pattern) . '\z';

        return (bool) preg_match('#^' . $pattern . '#', $value);
    }

}