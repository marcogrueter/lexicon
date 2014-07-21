<?php namespace Aiws\Lexicon\Provider\Laravel\Node;

use Aiws\Lexicon\Node\Single;

class Insert extends Single
{

    public $name = 'include';

    public function compile()
    {
        $attribute = $this->newAttributeParser()->compileAttribute('partial', 0);

        $share = $this->newAttributeParser()->compileShared([0 => 'partial']);

        if (!empty($attribute)) {
            return "<?php echo \$__env->make({$attribute},
            array_except(array_merge(get_defined_vars(), {$share}),array('__data','__path')))->render(); ?>";
        }

        return null;
    }

}