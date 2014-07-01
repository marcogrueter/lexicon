<?php namespace Aiws\Lexicon\Util;


use Aiws\Lexicon\Contract\NodeInterface;

class ConditionalParser
{
    protected $expression;

    /**
     * @var \Aiws\Lexicon\Contract\NodeInterface
     */
    protected $node;

    protected $source = '';

    protected $logicalOperatorPlaceholder = ' __LOGICAL__OPERATOR__ ';

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

    /**
     * Get
     *
     * @var array
     */
    protected $operatorReplacements = array(
        'equals' => '==',
        //'not equals' => '!',
        'not'    => '!',
    );

    /**
     * Logical operators found
     *
     * @var array
     */
    protected $logicalOperatorsFound = array();

    public $expressionArray;

    public $parsedExpression = '';

    public $parsedName;

    public $noParse = array(
        'true',
        'false',
        'null'
    );

    /**
     * Comparisons
     *
     * @var array
     */
    protected $comparisons = [];

    /**
     * @param               $expression
     * @param NodeInterface $node
     */
    public function __construct($expression, NodeInterface $node)
    {
        $this->node       = $node;
        $this->lexicon    = $node->getEnvironment();
        $this->expression = $this->lexicon->getRegex()->compress($expression);
        $this->parse();
    }

    public function getSpecialComparisonOperators()
    {
        return $this->lexicon->getConditionalHandler()->getSpecialComparisonOperators();
    }

    public function parse()
    {
        $this
            ->extractLogicalOperators($this->getLogicalOperatorsMatches())
            ->parseComparisons();
    }

    public function extractLogicalOperators($logicalOperatorMatches)
    {
        // Get logical operator matches
        foreach ($logicalOperatorMatches as $match) {
            $logicalOperator               = $match[1] == '||' ? '\|\|' : $match[1];
            $this->logicalOperatorsFound[] = $match[1];
            $this->expression              = preg_replace(
                '/' . $logicalOperator . '/',
                $this->logicalOperatorPlaceholder,
                $this->expression,
                1
            );
        }

        return $this;
    }

    public function getComparisons()
    {
        return explode($this->logicalOperatorPlaceholder, $this->expression);
    }

    public function parseComparisons()
    {
        foreach ($this->getComparisons() as $comparison) {
            $this->parseComparison($comparison);
        }
    }

    public function parseComparison($comparison)
    {
        $comparison = trim($this->replaceOperators($comparison));

        $hasNotOperator = (strpos($comparison, '! ') !== false);

        $hasExists = (strpos($comparison, 'exists ') !== false);

        $comparison = preg_replace('/! /', '', $comparison);
        $comparison = preg_replace('/\bexists\b/', '', $comparison);


        if ($operator = $this->getOperatorMatch($comparison)) {

            $parts = explode($operator, $comparison);

            if (count($parts) == 2) {
                $left  = $parts[0];
                $right = $parts[1];
            }

            $this->comparisons[] = $this->getComparisonSource($left, $right, $operator);

        } else {
            $this->comparisons[] = $this->getPartSource($part = $comparison);
        }

        return $this;
    }

    public function getPartSource($key)
    {
        $key = trim($key);

        // this shouldn't happen
        if (is_null($key) or is_array($key)) {
            return 'null';
        }

        if (in_array($key, ['null', 'true', 'false'])) {
            return $key;
        }

        if (preg_match('/^\'(.*)\'$/', $key, $matches)) {
            return "'{$matches[1]}'";
        }

        if (preg_match('/^"(.*)"$/', $key, $matches)) {
            return $matches[1];
        }

        if (preg_match('/^(\d+)$/', $key, $matches)) {
            return $matches[1];
        }

        if (preg_match('/^(\d[\d\.]+)$/', $key, $matches)) {
            return $matches[1];
        }

        $dataSource = '$' . $this->node->getParent()->getItemName();

        if ($this->node->getParent()->isRoot()) {
            $dataSource = $this->node->getEnvironment()->getEnvironmentVariable();
        }

        return "\$__lexicon->get({$dataSource}, '{$key}')";
    }

    public function getComparisonSource($left, $right, $operator = null)
    {
        return "\$__lexicon->compare({$this->getPartSource($left)}, {$this->getPartSource($right)}, '{$operator}') ";
    }

    public function getOperatorMatch($comparison)
    {
        $match = $this->lexicon->getRegex()->getMatch($comparison, $this->getComparisonOperatorsRegexMatcher());

        if (is_array($match) and !empty($match)) {
            return $match[1];
        }

        return false;
    }

    public function getLogicalOperatorsMatches()
    {
        return $this->lexicon->getRegex()->getMatches(
            $this->expression,
            $this->getLogicalOperatorsRegexMatcher()
        );
    }

    public function getRegexMatcher()
    {
        return '/\{\{\s*(' . implode('|', $this->startConditionals) . ')\s*((?:\()?(.*?)(?:\))?)\s*\}\}/ms';
    }

    public function getLogicalOperatorsRegexMatcher()
    {
        return '/\s*(' . implode('|', $this->logicalOperators) . ')\s/ms';
    }

    public function getComparisonOperatorsRegexMatcher()
    {
        return '/\s*(' . implode('|', array_merge($this->comparisonOperators, $this->getSpecialComparisonOperators())) . ')\s/ms';
    }

    protected function replaceOperators($comparison)
    {
        foreach ($this->operatorReplacements as $string => $replacementOperator) {
            $comparison = str_replace($string, $replacementOperator, $comparison);
        }

        return $comparison;
    }

    public function getSource()
    {
        foreach ($this->comparisons as $key => $comparisonSource) {
            $this->source .= $comparisonSource;
            if (isset($this->logicalOperatorsFound[$key])) {
                $this->source .= $this->logicalOperatorsFound[$key];
            }
        }

        return $this->source;
    }

}