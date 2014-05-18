<?php namespace Aiws\Lexicon\Node;

class Insert extends Single
{

    public $name = 'include';

    public function compile()
    {
        $name = $this->getAttribute('name');

        return "<?php echo \$__env->make('{$name}', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>";
    }

}