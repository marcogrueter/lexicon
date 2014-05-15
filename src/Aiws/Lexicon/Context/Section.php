<?php namespace Aiws\Lexicon\Context;

class Section extends Single
{
    public $name = 'section';

    public function compileContext()
    {
        $name = $this->getAttribute('name');

        return "<?php \$__env->startSection('{$name}'); ?>";
    }

}