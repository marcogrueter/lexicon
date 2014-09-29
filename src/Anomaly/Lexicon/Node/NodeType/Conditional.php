<?php namespace Anomaly\Lexicon\Node\NodeType;

use Anomaly\Lexicon\Conditional\ConditionalCompiler;
use Anomaly\Lexicon\Conditional\ConditionalParser;
use Anomaly\Lexicon\Conditional\ConditionalValidator;
use Anomaly\Lexicon\Conditional\Expression\ExpressionNode;
use Anomaly\Lexicon\Conditional\Validator\ElseifValidator;
use Anomaly\Lexicon\Conditional\Validator\IfValidator;
use Anomaly\Lexicon\Contract\Node\ConditionalInterface;

class Conditional extends Single implements ConditionalInterface
{
    /**
     * Start conditionals
     *
     * @var array
     */
    public $startConditionals = array(
        'if',
        'unless',
        'elseif',
        'elseunless'
    );

    /**
     * Expression
     *
     * @var string
     */
    protected $expression = '';

    /**
     * @return string
     */
    public function getNameMatcher()
    {
        return implode('|', $this->startConditionals);
    }

    /**
     * Setup properties using the regex matches
     *
     * @return void
     */
    public function setup()
    {
        $this
            ->setExpression($this->match(2))
            ->setExtractionContent($this->match(0))
            ->setName($this->match(1));
    }

    /**
     * Get construct name
     *
     * @return string
     */
    public function getConstructName()
    {
        return str_replace(['unless', 'elseunless'], ['if', 'elseif'], $this->getName());
    }

    /**
     * Get validator
     *
     * @return ConditionalValidator
     */
    public function getValidator()
    {
        return new ConditionalValidator($this);
    }

    /**
     * Get conditional parser
     *
     * @return ConditionalCompiler
     */
    public function getCompiler()
    {
        return new ConditionalCompiler($this);
    }

    /**
     * @param $expression
     * @return Conditional
     */
    public function setExpression($expression)
    {
        $this->expression = $expression;
        return $this;
    }

    /**
     * Get expression
     *
     * @return string
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * Get expression node
     *
     * @return ExpressionNode
     */
    public function getExpressionNode()
    {
        return new ExpressionNode($this->getLexicon());
    }

    /**
     * Compile source
     *
     * @return null|string
     */
    public function compile()
    {
        return "{$this->getCompiler()->getStart()} ({$this->getCompiler()->getExpression()}):";
    }

}