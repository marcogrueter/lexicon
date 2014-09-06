<?php namespace Anomaly\Lexicon\Node;

use Anomaly\Lexicon\Contract\NodeConditionalInterface;
use Anomaly\Lexicon\Conditional\Validator\EndifValidator;

class ConditionalEndif extends Single implements NodeConditionalInterface
{
    /**
     * Name
     *
     * @var string
     */
    public $name = 'endif';

    /**
     * Get setup from regex match
     *
     * @param array $match
     */
    public function setup(array $match)
    {
        $this
            ->setExtractionContent($match[0])
            ->setValidator(new EndifValidator($this));
    }

    /**
     * Compile source
     *
     * @return string
     */
    public function compile()
    {
        return "endif;";
    }

}