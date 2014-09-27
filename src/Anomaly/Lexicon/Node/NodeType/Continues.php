<?php namespace Anomaly\Lexicon\Node\NodeType;

use Anomaly\Lexicon\Stub\LexiconStub;

class Continues extends Single
{

    /**
     * @var string
     */
    protected $name = 'continue';

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->hasParentBlockNotRoot();
    }

    /**
     * @return string
     */
    public function compile()
    {
        return 'continue;';
    }

    /**
     * Stub for testing with PHPSpec
     *
     * @return \Anomaly\Lexicon\Contract\LexiconInterface|static
     */
    public static function stub()
    {
        return new static(LexiconStub::get());
    }
} 