<?php namespace Aiws\Lexicon\Util;

class ConditionalHandler
{
    protected $comparisons;

    protected $specialComparisons = [];

    public function __construct()
    {
        $this->registerSpecialComparison(
            'contains',
            function ($left, $right) {

                if (is_string($left) and is_string($right)) {
                    return strpos($left, $right) !== false;
                }

                return false;
            }
        );
    }

    public function registerSpecialComparison($key, \Closure $closure)
    {
        $this->specialComparisons[$key] = $closure;
        return $this;
    }

    public function getSpecialComparisons()
    {
        return $this->specialComparisons;
    }

    public function getSpecialComparisonOperators()
    {
        return array_keys($this->getSpecialComparisons());
    }

    public function compare($left, $right, $operator = null)
    {
        $operator = trim($operator);

        // regular rules
        switch ($operator) {
            case '===':
                return ($left === $right);

            case '!==':
                return ($left !== $right);

            case '==':
                return ($left == $right);

            case '!=':
                return ($left != $right);

            case '>=':
                return ($left >= $right);

            case '<=':
                return ($left <= $right);

            case '>':
                return ($left > $right);

            case '<':
                return ($left < $right);

            default:
                return $this->specialComparison($left, $right, $operator);
        }
    }

    public function specialComparison($left, $right, $operator = null)
    {
        if (isset($this->specialComparisons[$operator])) {
            return call_user_func_array($this->specialComparisons[$operator], [$left, $right]);
        }

        return false;
    }
}