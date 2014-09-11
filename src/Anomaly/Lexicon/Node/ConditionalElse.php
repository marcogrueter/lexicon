<?php namespace Anomaly\Lexicon\Node;

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
     * Get setup from regex match
     *
     * @param array $match
     * @return mixed|void
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