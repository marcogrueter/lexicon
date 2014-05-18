<?php namespace Aiws\Lexicon\Node;

class SectionYield extends Single
{

    public $name = 'yield';

    public function compile()
    {
        $name = $this->getAttribute('name');

        return "<?php echo \$__env->yieldContent('{$name}'); ?>";
    }

}