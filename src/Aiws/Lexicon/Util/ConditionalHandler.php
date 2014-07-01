<?php namespace Aiws\Lexicon\Util;

class ConditionalHandler
{
    protected $comparisons;

    protected $specialComparisons = [];

    protected $specialComparisonsClasses = [];

    public function registerSpecialComparison($key, \Closure $closure)
    {
        $this->specialComparisons[$key] = $closure;
        return $this;
    }

    public function registerSpecialComparisonClass($object)
    {
        if (is_object($object)) {
            $this->specialComparisonsClasses[] = $object;
        }
    }

    public function getSpecialComparisonsClasses()
    {
        return $this->specialComparisonsClasses;
    }

    public function getSpecialComparisons()
    {
        return $this->specialComparisons;
    }

    public function getSpecialComparisonOperators()
    {
        $operators = [];

        foreach($this->getSpecialComparisonsClasses() as $object) {
            $reflection = new \ReflectionClass($object);
            foreach($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                $operators[] = $method->name;
            }
        }

        return array_merge($operators, array_keys($this->getSpecialComparisons()));
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

        foreach($this->specialComparisonsClasses as $object) {
            if (method_exists($object, $operator)) {
                return $object->{$operator}($left, $right);
            }
        }

        return false;
    }
}