<?php namespace Aiws\Lexicon\Util\Conditional;

class ConditionalComparisons
{

    /**
     * Contains
     *
     * @param $left
     * @param $right
     * @return bool
     */
    public function contains($haystack, $needles)
    {
        if (!$this->validateStrings($haystack, $needles)) {
            return false;
        }

        foreach ((array)$needles as $needle) {
            if ($needle != '' && strpos($haystack, $needle) !== false) {
                return true;
            }
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
    public function startsWith($haystack, $needles)
    {
        if (!$this->validateStrings($haystack, $needles)) {
            return false;
        }

        foreach ((array)$needles as $needle) {
            if ($needle != '' && strpos($haystack, $needle) === 0) {
                return true;
            }
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
    public function endsWith($haystack, $needles)
    {
        if (!$this->validateStrings($haystack, $needles)) {
            return false;
        }

        foreach ((array)$needles as $needle) {
            if ($needle == substr($haystack, -strlen($needle))) {
                return true;
            }
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
        if (!$this->validateStrings($pattern, $value)) {
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

        return (bool)preg_match('#^' . $pattern . '#', $value);

        return false;
    }

    /**
     * Validate strings
     *
     * @param $left
     * @param $right
     * @return bool
     */
    protected function validateStrings($left, $right)
    {
        return (is_string($left) or is_string($right) or is_array($right));
    }

}