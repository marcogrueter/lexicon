<?php namespace Aiws\Lexicon\Provider\Laravel\Node;

use Aiws\Lexicon\Node\Single;

class SectionYield extends Single
{

    /**
     * Name
     *
     * @var string
     */
    public $name = 'yield';

    /**
     * Compile source
     *
     * @return string
     */
    public function compile()
    {
        return "<?php echo \$__env->yieldContent('{$this->newAttributeParser()->compileAttribute('name')}'); ?>";
    }

}