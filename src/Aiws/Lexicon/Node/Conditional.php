<?php namespace Aiws\Lexicon\Node;

use Aiws\Lexicon\Util\ConditionalParser;

class Conditional extends Single
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
     * Get setup
     *
     * @param array $match
     */
    public function getSetup(array $match)
    {
        $this
            ->setName($match[1])
            ->setExtractionContent($match[0])
            ->setExpression($match[2])
            ->setParser(new ConditionalParser($this->expression, $this));
    }

    /**
     * Conditional parser
     *
     * @param $parser
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
     * Compile source
     *
     * @return null|string
     */
    public function compile()
    {
        $hasConditionalEnd = false;

        foreach ($this->getParent()->getChildren() as $node) {
            if ($node instanceof ConditionalEnd) {
                $hasConditionalEnd = true;
                break;
            }
        }

        if ($hasConditionalEnd) {
            return "<?php {$this->parser->getStart()} ({$this->parser->getExpression()}): ?>";
        }

        return null;
    }

}