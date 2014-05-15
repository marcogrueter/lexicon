<?php namespace Aiws\Lexicon\Context;

class SectionEnd extends Single
{

    public $name = 'stop';

    public function compileContext()
    {
        return "<?php \$__env->stopSection(); ?>";
    }

}