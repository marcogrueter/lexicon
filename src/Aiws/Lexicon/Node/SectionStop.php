<?php namespace Aiws\Lexicon\Node;

class SectionStop extends Single
{

    public $name = 'stop';

    public function compile()
    {
        return "<?php \$__env->stopSection(); ?>";
    }

}