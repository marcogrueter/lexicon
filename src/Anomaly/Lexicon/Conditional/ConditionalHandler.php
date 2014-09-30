<?php namespace Anomaly\Lexicon\Conditional;

use Anomaly\Lexicon\Contract\Conditional\ConditionalHandlerInterface;

class ConditionalHandler implements ConditionalHandlerInterface
{
    /**
     * Test types - array of BooleanTestTypeInterface objects
     *
     * @var array
     */
    protected $testTypes = [];

    /**
     * Comparison operators
     *
     * @var array
     */
    protected $comparisonOperators = [
        '===',
        '!==',
        '==',
        '!=',
        '<=',
        '>=',
        '>',
        '<',
    ];

    /**
     * Register boolean test types
     *
     * @param array $booleanTestTypes
     * @return $this
     */
    public function registerBooleanTestTypes(array $booleanTestTypes)
    {
        $this->testTypes = $booleanTestTypes;
        return $this;
    }

    /**
     * Get comparison operators
     *
     * @return array
     */
    public function getComparisonOperators()
    {
        return $this->comparisonOperators;
    }

    /**
     * Get test types
     *
     * @return array
     */
    public function getTestTypes()
    {
        $testTypes = [];
        foreach($this->testTypes as $type => $class) {
            $testTypes[$type] = new $class;
        }
        return $testTypes;
    }

    /**
     * Get test operators
     *
     * @return array
     */
    public function getTestOperators()
    {
        $operators = $this->getComparisonOperators();
        foreach ($this->getTestTypes() as $testType) {;
            $operators = array_merge($operators, get_class_methods($testType));
        }
        return $operators;
    }

    /**
     * Boolean test
     *
     * @param      $left
     * @param      $right
     * @param null $operator
     * @return bool|mixed
     */
    public function booleanTest($left, $right, $operator)
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
                return $this->customTest($left, $right, $operator);
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
    public function customTest($left, $right, $operator = null)
    {
        $result = false;
        foreach ($this->getTestTypes() as $testType) {
            if (method_exists($testType, $operator)) {
                $result = $testType->{$operator}($left, $right);
                break;
            }
        }
        return $result;
    }

}