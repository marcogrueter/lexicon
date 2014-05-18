<?php namespace Aiws\Lexicon\Node;

class Section extends Single
{
    public $name = 'section';

    public function compile()
    {
        $name = $this->getAttribute('name');

        return "<?php \$__env->startSection('{$name}'); ?>";
    }

}