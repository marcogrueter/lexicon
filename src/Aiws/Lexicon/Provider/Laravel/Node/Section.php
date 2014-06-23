<?php namespace Aiws\Lexicon\Provider\Laravel\Node;

use Aiws\Lexicon\Node\Single;

class Section extends Single
{
    public $name = 'section';

    public function compile()
    {
        return "<?php \$__env->startSection('{$this->getAttribute('name')}'); ?>";
    }

}