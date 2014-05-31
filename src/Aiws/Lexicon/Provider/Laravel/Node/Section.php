<?php namespace Aiws\Lexicon\Provider\Laravel\Node;

use Aiws\Lexicon\Node\Single;

class Section extends Single
{
    public $name = 'section';

    public function compile()
    {
        $name = $this->getAttribute('name');

        return "<?php \$__env->startSection('{$name}'); ?>";
    }

}