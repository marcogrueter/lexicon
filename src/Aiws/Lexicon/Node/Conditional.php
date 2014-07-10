<?php namespace Aiws\Lexicon\Node;

use Aiws\Lexicon\Util\ConditionalParser;

class Conditional extends Single
{
    protected $conditionalParser;

    public $startConditionals = array(
        'if',
        'unless',
        'elseif',
        'elseunless'
    );

    protected $expression = '';

    public $parsedExpression = '';

    public $parsedName;

    /**
     * @var ConditionalParser
     */
    protected $parser;

    public function getNameMatcher()
    {
        return implode('|', $this->startConditionals);
    }

    public function getSetup(array $match)
    {
        $this
            ->setName($match[1])
            ->setExtractionContent($match[0]);

        $this->expression = $match[2];

        $this->parsedName = $match[1];

        if ($this->parsedName == 'unless') {
            $this->parsedName = 'if (!(';
        } elseif ($this->parsedName == 'elseunless') {
            $this->parsedName = 'elseif (!(';
        } else {
            $this->parsedName .= ' ((';
        }

        $this->parser = new ConditionalParser($this->expression, $this);
    }

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
            return "<?php if ({$this->parser->getSource()}): ?>";
        }

        return null;
    }

}