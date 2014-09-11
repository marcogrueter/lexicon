<?php namespace Anomaly\Lexicon\Node;

use Anomaly\Lexicon\Conditional\Validator\EndifValidator;
use Anomaly\Lexicon\Contract\Node\ConditionalEndInterface;

class ConditionalEndif extends Single implements ConditionalEndInterface
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
     * @return mixed|void
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