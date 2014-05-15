<?php namespace Aiws\Lexicon\Node;

class SectionShow extends Single
{

    public $name = 'show';

    public function compileNode()
    {
        return "<?php echo \$__env->yieldSection(); ?>";
    }

}