<?php namespace Anomaly\Lexicon\Conditional;

use Anomaly\Lexicon\Contract\Conditional\ConditionalHandlerInterface;

class ConditionalHandler implements ConditionalHandlerInterface
{

    /**
     * Get logical operators
     *
     * @var array
     */
    protected $logicalOperators = [
        'and',
        'or',
        '&&',
        '||',
    ];

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
     * Test types - array of BooleanTestTypeInterface objects
     *
     * @var array
     */
    protected $testTypes = [];

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
     * Get logical operators
     *
     * @return array
     */
    public function getLogicalOperators()
    {
        return $this->logicalOperators;
    }

    /**
     * @return string
     */
    public function getLogicalOperatorRegex()
    {
        return $this->operatorsToRegex($this->prepare($this->getLogicalOperators()));
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
        foreach ($this->testTypes as $type => $class) {
            $testTypes[$type] = new $class;
        }
        return $testTypes;
    }

    /**
     * Get test operators
     *
     * @return array
     */
    public function getCustomOperators()
    {
        $operators = [];
        foreach ($this->getTestTypes() as $testType) {
            $operators = array_merge($operators, get_class_methods($testType));
        }
        return $operators;
    }

    /**
     * Get test oprators
     *
     * @return array
     */
    public function getTestOperators()
    {
        return array_merge(
            $this->getComparisonOperators(),
            $this->getCustomOperators()
        );
    }

    /**
     * Get test operator regex
     *
     * @return string
     */
    public function getTestOperatorRegex()
    {
        return $this->operatorsToRegex($this->prepare($this->getTestOperators()));
    }

    /**
     * Prepare operators
     *
     * @param array $operators
     * @return array
     */
    public function prepare(array $operators)
    {
        foreach ($operators as &$operator) {
            if (preg_match('/\w+/', $operator)) {
                $operator = '\b' . $operator . '\b';
            } else {
                $operator = preg_quote($operator);
            }
        }
        return $operators;
    }

    /**
     * Operators to regex
     *
     * @param array $operators
     * @return string
     */
    public function operatorsToRegex(array $operators)
    {
        return '/(' . implode('|', $operators) . ')/';
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