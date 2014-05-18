<?php namespace Aiws\Lexicon\Node;

class SectionShow extends Single
{

    public $name = 'show';

    public function compile()
    {
        return "<?php echo \$__env->yieldSection(); ?>";
    }

}