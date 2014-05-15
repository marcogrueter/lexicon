<?php namespace Aiws\Lexicon\Context;

class SectionShow extends Single
{

    public $name = 'show';

    public function compileContext()
    {
        return "<?php echo \$__env->yieldSection(); ?>";
    }

}