<?php namespace Aiws\Lexicon\Node;

use Aiws\Lexicon\Contract\NodeConditionalInterface;
use Aiws\Lexicon\Util\Conditional\ConditionalParser;
use Aiws\Lexicon\Util\Conditional\ConditionalValidatorElseif;
use Aiws\Lexicon\Util\Conditional\ConditionalValidatorIf;

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
    public function getSetup(array $match)
    {
        $this
            ->setName($match[1])
            ->setExtractionContent($match[0])
            ->setExpression($match[2])
            ->setParser(new ConditionalParser($this));

        if ($this->getName() == 'if') {
            $this->setValidator(new ConditionalValidatorIf($this));
        } elseif ($this->getName('elseif')) {
            $this->setValidator(new ConditionalValidatorElseif($this));
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
        return "<?php {$this->parser->getStart()} ({$this->parser->getExpression()}): ?>";
    }

}