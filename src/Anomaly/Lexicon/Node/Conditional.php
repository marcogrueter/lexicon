<?php namespace Anomaly\Lexicon\Node;

use Anomaly\Lexicon\Contract\NodeConditionalInterface;
use Anomaly\Lexicon\Conditional\ConditionalParser;
use Anomaly\Lexicon\Conditional\Validator\ElseifValidator;
use Anomaly\Lexicon\Conditional\Validator\IfValidator;

class Conditional extends Single implements NodeConditionalInterface
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
     * Conditional parser
     *
     * @var ConditionalParser
     */
    protected $parser;

    /**
     * @return string
     */
    public function getNameMatcher()
    {
        return implode('|', $this->startConditionals);
    }

    /**
     * Get setup from regex match
     *
     * @param array $match
     * @return void
     */
    public function setup(array $match)
    {
        $this
            ->setName($match[1])
            ->setExtractionContent($match[0])
            ->setExpression($match[2])
            ->setParser(new ConditionalParser($this));

        if ($this->getName() == 'if') {
            $this->setValidator(new IfValidator($this));
        } elseif ($this->getName('elseif')) {
            $this->setValidator(new ElseifValidator($this));
        }
    }

    /**
     * Set conditional parser
     *
     * @param $parser ConditionalParser
     * @return ConditionalParser
     */
    public function setParser($parser)
    {
        $this->parser = $parser;
        return $this;
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
     * Compile source
     *
     * @return null|string
     */
    public function compile()
    {
        return "{$this->parser->getStart()} ({$this->parser->getExpression()}):";
    }

}