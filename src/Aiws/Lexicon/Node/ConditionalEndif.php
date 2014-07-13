<?php namespace Aiws\Lexicon\Node;

use Aiws\Lexicon\Contract\NodeConditionalInterface;
use Aiws\Lexicon\Util\Conditional\ConditionalValidatorEndif;

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
    public function getSetup(array $match)
    {
        $this
            ->setExtractionContent($match[0])
            ->setValidator(new ConditionalValidatorEndif($this));
    }

    /**
     * Compile source
     *
     * @return string
     */
    public function compile()
    {
        return '<?php endif; ?>';
    }

}