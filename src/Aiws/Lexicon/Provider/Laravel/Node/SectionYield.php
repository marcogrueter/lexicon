<?php namespace Aiws\Lexicon\Provider\Laravel\Node;

use Aiws\Lexicon\Node\Single;

class SectionYield extends Single
{

    public $name = 'yield';

    public function compile()
    {
        $name = $this->getAttribute('name');

        return "<?php echo \$__env->yieldContent('{$name}'); ?>";
    }

}