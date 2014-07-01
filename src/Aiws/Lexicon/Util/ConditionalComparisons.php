<?php namespace Aiws\Lexicon\Util;

class ConditionalComparisons
{

    /**
     * Contains
     *
     * @param $left
     * @param $right
     * @return bool
     */
    public function contains($left, $right)
    {
        if (is_string($left) and is_string($right)) {
            return strpos($left, $right) !== false;
        }

        return false;
    }

}