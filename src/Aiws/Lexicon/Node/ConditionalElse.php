<?php namespace Aiws\Lexicon\Node;

use Aiws\Lexicon\Contract\NodeConditionalInterface;
use Aiws\Lexicon\Util\Conditional\ConditionalValidator;
use Aiws\Lexicon\Util\Conditional\ConditionalValidatorElse;
use Aiws\Lexicon\Util\Conditional\ConditionalValidatorIf;

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
            ->setValidator(new ConditionalValidatorElse($this));
    }

    /**
     * Compile source
     *
     * @return string
     */
    public function compile()
    {
        return '<?php else: ?>';
    }

}