<?php namespace Aiws\Lexicon\Provider\Laravel\Node;

use Aiws\Lexicon\Node\Single;

class SectionStop extends Single
{

    /**
     * Name
     *
     * @var string
     */
    public $name = 'stop';

    /**
     * Compile source
     *
     * @return string
     */
    public function compile()
    {
        return "\$__env->stopSection();";
    }

}