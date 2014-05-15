<?php namespace Aiws\Lexicon\Node;

class Section extends Single
{
    public $name = 'section';

    public function compileNode()
    {
        $name = $this->getAttribute('name');

        return "<?php \$__env->startSection('{$name}'); ?>";
    }

}