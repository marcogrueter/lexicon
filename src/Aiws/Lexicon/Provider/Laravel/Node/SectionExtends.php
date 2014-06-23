<?php namespace Aiws\Lexicon\Provider\Laravel\Node;

use Aiws\Lexicon\Node\Single;

class SectionExtends extends Single
{
    public $name = 'extends';

    public function compile()
    {
        $rootNode = $this->getRootNode();

        $rootNode->footer[] = "<?php echo \$__env->make('{$this->getAttribute(
            'layout'
        )}', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>";

        return null;
    }

}