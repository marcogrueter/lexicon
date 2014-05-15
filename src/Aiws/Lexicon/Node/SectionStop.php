<?php namespace Aiws\Lexicon\Node;

class SectionStop extends Single
{

    public $name = 'stop';

    public function compileNode()
    {
        return "<?php \$__env->stopSection(); ?>";
    }

}