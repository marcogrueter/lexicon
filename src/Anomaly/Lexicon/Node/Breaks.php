<?php namespace Anomaly\Lexicon\Node;

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

} 