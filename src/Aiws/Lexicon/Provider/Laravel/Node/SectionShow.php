<?php namespace Aiws\Lexicon\Provider\Laravel\Node;

use Aiws\Lexicon\Node\Single;

class SectionShow extends Single
{

    /**
     * Name
     *
     * @var string
     */
    public $name = 'show';

    /**
     * Compile source
     *
     * @return string
     */
    public function compile()
    {
        return "<?php echo \$__env->yieldSection(); ?>";
    }

}