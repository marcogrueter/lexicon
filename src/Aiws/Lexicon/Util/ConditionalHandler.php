<?php namespace Aiws\Lexicon\Util;

class ConditionalHandler
{
    protected $comparisons = [];

    public function registerComparison($key, \Closure $closure)
    {
        $this->comparisons[$key] = $closure;
        return $this;
    }

    public function registerComparisons(array $comparisons)
    {
        foreach($comparisons as $key => $comparison) {
            $this->registerComparison($key, $comparison);
        }
        return $this;
    }

    public function comparison($left, $right, $op = null)
    {

    }
}