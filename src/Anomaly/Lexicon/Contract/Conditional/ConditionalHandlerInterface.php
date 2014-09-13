<?php namespace Anomaly\Lexicon\Contract\Conditional;

interface ConditionalHandlerInterface
{
    /**
     * @param array $booleanTestTypes
     * @return ConditionalHandlerInterface
     */
    public function registerBooleanTestTypes(array $booleanTestTypes);

    /**
     * @return array
     */
    public function getTestOperators();

    /**
     * Compare values
     *
     * @param      $left
     * @param      $right
     * @param null $operator
     * @return bool
     */
    public function compare($left, $right, $operator = null);

    /**
     * Custom test
     *
     * @param      $left
     * @param      $right
     * @param null $operator
     * @return mixed
     */
    public function customTest($left, $right, $operator = null)
} 