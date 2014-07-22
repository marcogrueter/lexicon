<?php namespace Aiws\Lexicon\Provider\Laravel\Node;

use Aiws\Lexicon\Node\Single;

class Section extends Single
{

    /**
     * Name
     *
     * @var string
     */
    public $name = 'section';

    /**
     * Compile source
     *
     * @return string
     */
    public function compile()
    {
        return "<?php \$__env->startSection({$this->newAttributeParser()->compileAttribute('name')}); ?>";
    }

}