<?php namespace Aiws\Lexicon\Context;

class SectionExtends extends Single
{
    public $name = 'extends';

    public function compileContext()
    {
        $rootNode = $this->getRootNode();

        $view = $this->getAttribute('layout');

        $data = "<?php echo \$__env->make('{$view}', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>";

        $rootNode->footer[] = $data;

        return null;
    }

}