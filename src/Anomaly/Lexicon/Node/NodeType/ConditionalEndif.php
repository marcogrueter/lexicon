<?php namespace Anomaly\Lexicon\Node\NodeType;

use Anomaly\Lexicon\Conditional\Validator\EndifValidator;
use Anomaly\Lexicon\Contract\Node\ConditionalEndInterface;

class ConditionalEndif extends Conditional implements ConditionalEndInterface
{
    /**
     * Name
     *
     * @var string
     */
    public $name = 'endif';

    /**
     * Setup properties using the regex matches
     *
     * @return void
     */
    public function setup()
    {
        $this->setExtractionContent($this->match(0));
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