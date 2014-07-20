<?php namespace Aiws\Lexicon\Util\Conditional;

use Aiws\Lexicon\Contract\TestTypeInterface;

class ConditionalHandler
{

    /**
     * Tests - array of closures
     *
     * @var array
     */
    protected $tests = [];

    /**
     * Test types - array of TestTypeInterface objects
     *
     * @var array
     */
    protected $testTypes = [];

    /**
     * Register test
     *
     * @param          $key
     * @param callable $closure
     * @return $this
     */
    public function registerTest($key, \Closure $closure)
    {
        $this->tests[$key] = $closure;
        return $this;
    }

    /**
     * Register test type
     *
     * @param TestTypeInterface $test
     * @return ConditionalHandler
     */
    public function registerTestType(TestTypeInterface $test)
    {
        if ($type = $test->getType()) {
            $this->testTypes[$type] = $test;
        } elseif (is_null($type)) {
            $this->testTypes[] = $test;
        }

        return $this;
    }

    /**
     * Get test types
     *
     * @return array
     */
    public function getTestTypes()
    {
        return $this->testTypes;
    }

    /**
     * Get tests array
     *
     * @return array
     */
    public function getTests()
    {
        return $this->tests;
    }

    /**
     * Get test
     *
     * @param $operator
     * @return \Closure|null
     */
    public function getTest($operator)
    {
        if (isset($this->tests[$operator])) {
            return $this->tests[$operator];
        }

        return null;
    }

    /**
     * Get test operators
     *
     * @return array
     */
    public function getTestOperators()
    {
        $operators = [];

        foreach ($this->getTestTypes() as $testType) {
            $reflection = new \ReflectionClass($testType);
            foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                $operators[] = $method->name;
            }
        }

        return array_merge($operators, array_keys($this->getTests()));
    }

    /**
     * Compare
     *
     * @param      $left
     * @param      $right
     * @param null $operator
     * @return bool|mixed
     */
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
                return $this->runTest($left, $right, $operator);
        }
    }

    /**
     * Run test
     *
     * @param      $left
     * @param      $right
     * @param null $operator
     * @return bool|mixed
     */
    public function runTest($left, $right, $operator = null)
    {
        if ($test = $this->getTest($operator)) {
            return call_user_func_array($test, [$left, $right]);
        }

        foreach ($this->getTestTypes() as $testType) {
            if (method_exists($testType, $operator)) {
                return $testType->{$operator}($left, $right);
            }
        }

        return false;
    }
}