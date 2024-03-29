<?php namespace Anomaly\Lexicon\Node\NodeType;

use Anomaly\Lexicon\Conditional\Validator\ElseValidator;
use Anomaly\Lexicon\Contract\Node\ConditionalInterface;

class ConditionalElse extends Conditional
{

    /**
     * @return string
     */
    public function getNameMatcher()
    {
        return 'else';
    }

    /**
     * Setup properties using the regex matches
     *
     * @return void
     */
    public function setup()
    {
        $this
            ->setName($this->match(1))
            ->setExtractionContent($this->match(0));
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