<?php namespace Anomaly\Lexicon\Node;

use Anomaly\Lexicon\Contract\NodeConditionalInterface;
use Anomaly\Lexicon\Conditional\Validator\ElseValidator;

class ConditionalElse extends Single implements NodeConditionalInterface
{
    /**
     * Name
     *
     * @var string
     */
    public $name ='else';

    /**
     * Get setup from regex match
     *
     * @param array $match
     */
    public function getSetup(array $match)
    {
        $this
            ->setExtractionContent($match[0])
            ->setValidator(new ElseValidator($this));
    }

    /**
     * Compile source
     *
     * @return string
     */
    public function compile()
    {
        return "else:";
    }

}