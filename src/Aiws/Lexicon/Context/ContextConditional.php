<?php namespace Aiws\Lexicon\Context;

class ContextConditional extends ContextType
{
    public $startConditionals = array(
        'if',
        'unless',
        'elseif',
        'elseunless'
    );

    protected $logicalOperators = array(
        'and',
        'or',
        '&&',
        '\|\|',
    );

    protected $comparisonOperators = array(
        '===',
        '!==',
        '==',
        '!=',
        '<=',
        '>=',
        '>',
        '<',
    );

    protected $operatorReplacements = array(
        'equals' => '==',
        //'not equals' => '!',
        'not'    => '!',
    );

    protected $logicalOperatorsFound = array();

    public $expression;

    public $expressionArray;

    public $parsedExpression = '';

    public $parsedName;

    public $noParse = array(
        'true',
        'false',
        'null'
    );

    public function getRegex()
    {
        return '/\{\{\s*(' . implode('|', $this->startConditionals) . ')\s*((?:\()?(.*?)(?:\))?)\s*\}\}/ms';
    }

    public function getLogicalOperatorsRegex()
    {
        return '/\s*(' . implode('|', $this->logicalOperators) . ')\s/ms';
    }

    public function getComparisonOperatorsRegex()
    {
        return '/\s*(' . implode('|', $this->comparisonOperators) . ')\s/ms';
    }

    public function getSetup(array $match)
    {
        $this->name              = $this->parsedName = $match[1];
        $this->extractionContent = $match[0];
        $this->expression        = $this->compress($match[2]);

        if ($this->parsedName == 'unless') {
            $this->parsedName = 'if (!(';
        } elseif ($this->parsedName == 'elseunless') {
            $this->parsedName = 'elseif (!(';
        } else {
            $this->parsedName .= ' ((';
        }
    }

    public function compileParentContext($parsedParentContent)
    {
        $this->expression = $this->replaceOperators($this->expression);

        $logicalOperatorMatches = $this->getMatches($this->expression, $this->getLogicalOperatorsRegex());

        // Get logical operator matches
        foreach ($logicalOperatorMatches as $match) {
            $logicalOperator               = $match[1] == '||' ? '\|\|' : $match[1];
            $this->logicalOperatorsFound[] = $match[1];
            $this->expression              = preg_replace(
                '/' . $logicalOperator . '/',
                ' __LOGICAL_OPERATOR__ ',
                $this->expression,
                1
            );
        }

        $comparisonSegments = explode(' __LOGICAL_OPERATOR__ ', $this->expression);

        foreach ($comparisonSegments as $key => $comparison) {

            $comparison = $this->replaceOperators($comparison);

            $comparison = trim($comparison);

            $hasNotOperator = (strpos($comparison, '! ') !== false);

            $hasExists = (strpos($comparison, 'exists ') !== false);

            $comparison = preg_replace('/! /', '', $comparison);
            $comparison = preg_replace('/\bexists\b/', '', $comparison);

            $matches = $this->getMatches($comparison, $this->getComparisonOperatorsRegex());

            $comparisonOperator = null;

            if (isset($matches[0]) and isset($matches[0][1])) {

                $comparisonOperator = $matches[0][1];

                $values = explode(' ' . $comparisonOperator . ' ', $comparison);

                $values = array_map(
                    function ($value) {
                        return array(
                            'raw' => trim($value),
                            'php' => $this->getVariablePhp($value),
                        );
                    },
                    $values
                );

                if ((count($values) == 2 and $values[0]['php'] and $values[1]['php'])) {
                    $this->expressionArray[] = array(
                        'text'                => $comparison,
                        'comparison_operator' => $comparisonOperator,
                        'values'              => $values,
                        'has_not'             => $hasNotOperator,
                        'has_exists'          => $hasExists,
                    );
                }

            } else {

                if ($parsedComparison = $this->getVariablePhp($comparison)) {

                    $this->expressionArray[] = array(
                        'text'                => $comparison,
                        'comparison_operator' => false,
                        'values'              => $parsedComparison,
                        'has_not'             => $hasNotOperator,
                        'has_exists'          => $hasExists,
                    );
                }
            }
        }

        if (!empty($this->expressionArray)) {
            foreach ($this->expressionArray as $key => $comparison) {

                $logicalOperator = null;

                if (isset($this->logicalOperatorsFound[$key])) {
                    $logicalOperator = $this->logicalOperatorsFound[$key];
                }

                $not = $comparison['has_not'] ? '! ' : null;

                $hasExists = $comparison['has_exists'] ? 'isset' : null;

                if ($comparison['comparison_operator']) {
                    $this->parsedExpression .=
                        $not . $comparison['values'][0]['php'] . ' ' .
                        $comparison['comparison_operator'] . ' ' .
                        $comparison['values'][1]['php'] . ' ' .
                        $logicalOperator . ' ';
                } else {

                    if ($comparison['values'] != 'null') {
                        $comparisonValue = $hasExists . '(' . $comparison['values'] . ')';
                    } else {
                        $comparisonValue = 'null';
                    }

                    $this->parsedExpression .= $not . $comparisonValue
                        . ' ' . $logicalOperator . ' ';
                }

            }

        } else {
            $this->trash         = true;
            $parsedParentContent = str_replace($this->extractionContent, '', $parsedParentContent);
        }

        return $parsedParentContent;
    }

    protected function replaceOperators($comparison)
    {
        foreach ($this->operatorReplacements as $word => $replacementOperator) {
            $comparison = str_replace($word, $replacementOperator, $comparison);
        }

        return $comparison;
    }

    public function getVariablePhp($value)
    {
        $dataParser   = $this->data();
        $value        = trim($value);
        $variableName = $value;

        if (preg_match('/\'' . $this->getVariableRegex() . '\'/s', $value)) {
            return $value;
        }

        if (!is_numeric($value) and !in_array($value, $this->noParse)) {

            $property = $dataParser->getPropertyData($this->data, $value);

            if ($this->parent and $loopVariable = $dataParser->getVariable(
                    $this->parent->data,
                    $this->parent->name
                ) and $dataParser->hasIterator($loopVariable)
            ) {

                if ($dataParser->hasArrayKey($loopVariable[0], $value)) {
                    $variableName = "\${$this->parent->getItem()}['{$value}']";
                } elseif ($dataParser->hasObjectKey($loopVariable[0], $value)) {
                    $variableName = "\${$this->parent->getItem()}->{$value}";
                } else {
                    $variableName = 'null';
                }

            } else {

                if (is_null($property['value'])) {
                    $variableName = 'null';
                } else {
                    $variableName = "\${$property['variable']}{$property['property']}";
                }

            }
        }

        return $variableName;
    }

    public function compileContext()
    {
        $hasConditionalEnd = false;

        foreach ($this->parent->children as $context) {
            if ($context instanceof ContextConditionalEnd) {
                $hasConditionalEnd = true;
                break;
            }
        }

        if ($hasConditionalEnd and !empty($this->data)) {
            return $this->php("{$this->parsedName} {$this->compress($this->parsedExpression)})):");
        }

        return null;
    }

}