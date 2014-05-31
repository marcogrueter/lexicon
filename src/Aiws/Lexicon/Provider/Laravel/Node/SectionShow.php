<?php namespace Aiws\Lexicon\Provider\Laravel\Node;

use Aiws\Lexicon\Node\Single;

class SectionShow extends Single
{

    public $name = 'show';

    public function compile()
    {
        return "<?php echo \$__env->yieldSection(); ?>";
    }

}