<?php namespace Aiws\Lexicon\Provider\Laravel\Node;

use Aiws\Lexicon\Node\Single;

class SectionStop extends Single
{

    public $name = 'stop';

    public function compile()
    {
        return "<?php \$__env->stopSection(); ?>";
    }

}