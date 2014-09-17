<?php namespace Anomaly\Lexicon\Conditional;

use Anomaly\Lexicon\Contract\Node\ConditionalInterface;
use Anomaly\Lexicon\Contract\Node\NodeInterface;
use Anomaly\Lexicon\Node\Variable;
use Anomaly\Lexicon\Support\ValueResolver;

class ConditionalCompiler
{
    protected $expression;

    /**
     * @var NodeInterface
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

    /**
     * @var array
     */
    public $startConditionals = array(
        'if',
        'unless',
        'elseif',
        'elseunless'
    );

    /**
     * @var array
     */
    protected $logicalOperators = array(
        'and',
        'or',
        '&&',
        '\|\|',
    );

    /**
     * @var array
     */
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

    /**
     * @var
     */
    public $expressionArray;

    /**
     * @var string
     */
    public $parsedExpression = '';

    /**
     * @var
     */
    public $parsedName;

    /**
     * @var array
     */
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
     * @param NodeInterface $node
     * @internal param $expression
     */
    public function __construct(ConditionalInterface $node)
    {
        $this->node         = $node;
        $this->lexicon      = $node->getLexicon();
        $this->expression   = $this->node->compress($node->getExpression());
        $this->variableNode = new Variable($node->getLexicon());
        $this->start        = $node->getName();


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
     * @return ConditionalCompiler
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

    /**
     * @return array
     */
    public function getComparisons()
    {
        return explode($this->logicalOperatorPlaceholder, $this->expression);
    }

    /**
     * Parse comparisons
     */
    public function parseComparisons()
    {
        foreach ($this->getComparisons() as $comparison) {
            $this->parseComparison($comparison);
        }
    }

    /**
     * Parse comparison
     *
     * @param $comparison
     * @return $this
     */
    public function parseComparison($comparison)
    {
        $hasNotOperator = (strpos($comparison, '! ') !== false);

        $not = $hasNotOperator ? '!' : null;

        $comparison = preg_replace('/! /', '', $comparison);

        if ($operator = $this->getOperatorMatch($comparison)) {

            $parts = explode($operator, $comparison);

            $left  = null;
            $right = null;

            if (count($parts) == 2) {
                $left  = $parts[0];
                $right = $parts[1];
            }

            $this->comparisons[] = $not . $this->getComparisonSource($left, $right, $operator);

        } else {

            $this->comparisons[] = $not . $this->getPartSource($part = $comparison);
        }

        return $this;
    }

    /**
     * Get part source
     *
     * @param $key
     * @return string
     */
    public function getPartSource($key)
    {
        $key = $this->newValueResolver($key);

        $finder = $this->variableNode->make([], $this->node->getParent())->setName($key)->getNodeFinder();

        return "\$__data['__env']->variable({$finder->getItemSource()}, '{$finder->getName()}')";
    }

    /**
     * Get comparison source
     *
     * @param      $left
     * @param      $right
     * @param null $operator
     * @return string
     */
    public function getComparisonSource($left, $right, $operator = null)
    {
        return "\$__data['__env']->compare({$this->getPartSource($left)}, {$this->getPartSource(
            $right
        )}, '{$operator}') ";
    }

    /**
     * @param $comparison
     * @return bool
     */
    public function getOperatorMatch($comparison)
    {
        $match = $this->node->getMatch($comparison, $this->getComparisonOperatorsRegexMatcher());

        if (is_array($match) and !empty($match)) {
            return $match[1];
        }

        return false;
    }

    /**
     * @return array
     */
    public function getLogicalOperatorsMatches()
    {
        return $this->node->getMatches(
            $this->expression,
            $this->getLogicalOperatorsRegexMatcher()
        );
    }

    /**
     * @return string
     */
    public function regex()
    {
        return '/\{\{\s*(' . implode('|', $this->startConditionals) . ')\s*((?:\()?(.*?)(?:\))?)\s*\}\}/ms';
    }

    /**
     * @return string
     */
    public function getLogicalOperatorsRegexMatcher()
    {
        return '/\s*(' . implode('|', $this->logicalOperators) . ')\s/ms';
    }

    /**
     * @return string
     */
    public function getComparisonOperatorsRegexMatcher()
    {
        return '/\s*(' . implode(
            '|',
            array_merge($this->comparisonOperators, $this->getTestOperators())
        ) . ')\s/ms';
    }

    /**
     * Replacement operators
     *
     * @param $comparison
     * @return mixed
     */
    protected function replaceOperators($comparison)
    {
        foreach ($this->operatorReplacements as $string => $replacementOperator) {
            $comparison = str_replace($string, $replacementOperator, $comparison);
        }

        return $comparison;
    }

    /**
     * Get start
     *
     * @return string
     */
    public function getStart()
    {
        $start = $this->start;

        if ($this->start == 'unless') {

            $start = 'if';

        } elseif ($this->start == 'unless') {

            $start = 'elseif';

        }

        return $start;
    }

    /**
     * Get expression
     *
     * @return string
     */
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

    /**
     * New Value resolver
     *
     * @return ValueResolver
     */
    public function newValueResolver()
    {
        return new ValueResolver();
    }

}