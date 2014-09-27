<?php namespace Anomaly\Lexicon\Node\NodeType;

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

} 