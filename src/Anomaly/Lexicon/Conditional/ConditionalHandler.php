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
     * Register test type
     *
     * @param $name
     * @param $booleanTestType
     * @internal param string $test
     * @return ConditionalHandler
     */
    public function registerBooleanTestType($name, $booleanTestType)
    {
        $this->testTypes[$name] = $booleanTestType;
        return $this;
    }

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
     * Get test types
     *
     * @return array
     */
    public function getTestTypes()
    {
        $testTypes = [];

        foreach($this->testTypes as $type => $class) {
            $testType[$type] = new $class;
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
        $operators = [];

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