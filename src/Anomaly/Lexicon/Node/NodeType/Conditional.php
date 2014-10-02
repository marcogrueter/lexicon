<?php namespace Anomaly\Lexicon\Node\NodeType;

use Anomaly\Lexicon\Conditional\ConditionalCompiler;
use Anomaly\Lexicon\Conditional\ConditionalValidator;
use Anomaly\Lexicon\Conditional\Expression\ExpressionNode;
use Anomaly\Lexicon\Contract\Node\ConditionalInterface;
use Anomaly\Lexicon\Contract\Node\NodeInterface;
use Anomaly\Lexicon\Stub\LexiconStub;

class Conditional extends Single implements ConditionalInterface
{
    /**
     * Start conditionals
     *
     * @var array
     */
    public $startConditionals = array(
        'if',
        'elseif',
        'unless',
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
            ->setCurrentContent($this->match(2))
            ->setContent($this->match(0))
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
     * Get expression node
     *
     * @return ExpressionNode
     */
    public function getExpressionNode()
    {
        return $this->getNodeFactory()->make(new ExpressionNode($this->getLexicon()), [$this->getContent()], $this)->createChildNodes();
    }

    /**
     * Compile source
     *
     * @return null|string
     */
    public function compile()
    {
        return "{$this->getConstructName()}({$this->compileExpression($this->getName())}):";
    }

    /**
     * @param $name
     * @return string
     */
    public function compileExpression($name)
    {
        $source = $this->getExpressionNode()->compile();
        if (in_array($name, ['unless', 'elseunless'])) {
            $source = "!({$source})";
        }
        return $source;
    }

    public static function stub()
    {
        return new static(LexiconStub::get());
    }

}