<?php namespace Aiws\Lexicon\Node;

class SectionExtends extends Single
{
    public $name = 'extends';

    public function compile()
    {
        $rootNode = $this->getRootNode();

        $view = $this->getAttribute('layout');

        $data = "<?php echo \$__env->make('{$view}', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>";

        $rootNode->footer[] = $data;

        return null;
    }

}