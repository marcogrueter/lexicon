<?php namespace Anomaly\Lexicon\Node\NodeType;

use Anomaly\Lexicon\Stub\LexiconStub;

class Breaks extends Single
{

    /**
     * @var string
     */
    protected $name = 'break';

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
        return 'break;';
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