<?php namespace Anomaly\Lexicon\Node;

use Anomaly\Lexicon\Conditional\Validator\ElseValidator;
use Anomaly\Lexicon\Contract\Node\ConditionalInterface;

class ConditionalElse extends Single implements ConditionalInterface
{
    /**
     * Name
     *
     * @var string
     */
    public $name = 'else';

    /**
     * Get setup from regex match
     *
     * @param array $match
     */
    public function setup(array $match)
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