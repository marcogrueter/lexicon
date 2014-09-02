<?php namespace Anomaly\Lexicon\Conditional;

use Anomaly\Lexicon\Contract\NodeInterface;
use Anomaly\Lexicon\Node\Variable;

class ConditionalParser
{
    protected $expression;

    /**
     * @var \Anomaly\Lexicon\Contract\NodeInterface
     */
    protected $node;

    /**
     * @var string
     */
    protected $source = '';

    /**
     * Logical operator placeholder
     *
     * @var string
     */
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
        'equals'       => '==',
        'not equals'   => '!=',
        'not'          => '!',
        'greater than' => '>',
        'less than'    => '<',
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

    public $noParseKey = array(
        'null',
        'true',
        'false',
    );

    /**
     * Comparisons
     *
     * @var array
     */
    protected $comparisons = [];

    /**
     * Variable node
     *
     * @var \Anomaly\Lexicon\Node\Variable
     */
    protected $variableNode;

    /**
     * Start conditional
     *
     * @var
     */
    protected $start;

    /**
     * @param               $expression
     * @param NodeInterface $node
     */
    public function __construct(NodeInterface $node)
    {
        $this->node         = $node;
        $this->lexicon      = $node->getLexicon();
        $this->expression   = $this->lexicon->getRegex()->compress($node->getExpression());
        $this->variableNode = new Variable($node->getLexicon());
        $this->variableNode->setEnvironment($this->lexicon);
        $this->start = $node->getName();


        $this->parse();
    }

    /**
     * @return mixed
     */
    public function getTestOperators()
    {
        return $this->lexicon->getConditionalHandler()->getTestOperators();
    }

    /**
     * @return ConditionalParser
     */
    public function parse()
    {
        $this->expression = str_replace(
            array_keys($this->operatorReplacements),
            array_values($this->operatorReplacements),
            $this->expression
        );

        $this
            ->extractLogicalOperators($this->getLogicalOperatorsMatches())
            ->parseComparisons();
        return $this;
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
        $hasNotOperator = (strpos($comparison, '! ') !== false);

        $not = $hasNotOperator ? '!' : null;

        $comparison = preg_replace('/! /', '', $comparison);

        if ($operator = $this->getOperatorMatch($comparison)) {

            $parts = explode($operator, $comparison);

            $left = null;
            $right = null;

            if (count($parts) == 2) {
                $left  = $parts[0];
                $right = $parts[1];
            }

            $this->comparisons[] = $not.$this->getComparisonSource($left, $right, $operator);

        } else {

            $this->comparisons[] = $not.$this->getPartSource($part = $comparison);
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

        if (in_array($key, $this->noParseKey)) {
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

        $finder = $this->variableNode->make(['name' => $key], $this->node->getParent())->getContextFinder();

        return "\$this->view()->variable({$finder->getItemName()}, '{$finder->getName()}')";
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

    public function regex()
    {
        return '/\{\{\s*(' . implode('|', $this->startConditionals) . ')\s*((?:\()?(.*?)(?:\))?)\s*\}\}/ms';
    }

    public function getLogicalOperatorsRegexMatcher()
    {
        return '/\s*(' . implode('|', $this->logicalOperators) . ')\s/ms';
    }

    public function getComparisonOperatorsRegexMatcher()
    {
        return '/\s*(' . implode(
            '|',
            array_merge($this->comparisonOperators, $this->getTestOperators())
        ) . ')\s/ms';
    }

    protected function replaceOperators($comparison)
    {
        foreach ($this->operatorReplacements as $string => $replacementOperator) {
            $comparison = str_replace($string, $replacementOperator, $comparison);
        }

        return $comparison;
    }

    public function getStart()
    {
        switch ($this->start) {
            case 'unless':
                return 'if';
            case 'elseunless':
                return 'elseif';
            default:
                return $this->start;
        }
    }

    public function getExpression()
    {
        foreach ($this->comparisons as $key => $comparisonSource) {
            $this->source .= $comparisonSource;
            if (isset($this->logicalOperatorsFound[$key])) {
                $this->source .= ' ' . $this->logicalOperatorsFound[$key] . ' ';
            }
        }

        return $this->source;
    }

}