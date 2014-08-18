<?php namespace Aiws\Lexicon\Conditional\Test;

use Aiws\Lexicon\Contract\TestTypeInterface;

class TestType implements TestTypeInterface
{
    /**
     * The test type
     *
     * @var string|null
     */
    protected $type = null;

    /**
     * Get the test type
     *
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }

}