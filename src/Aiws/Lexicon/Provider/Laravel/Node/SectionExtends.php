<?php namespace Aiws\Lexicon\Provider\Laravel\Node;

use Aiws\Lexicon\Node\Single;

class SectionExtends extends Single
{
    /**
     * Name
     *
     * @var string
     */
    public $name = 'extends';

    /**
     * Compile source
     *
     * @return null|string
     */
    public function compile()
    {
        $rootNode = $this->getRootNode();

        $attribute = $this->newAttributeParser()->compileAttribute('name', 0);

        if (!empty($attribute)) {
            $rootNode->footer[] = "<?php echo \$__env->make({$attribute},
            {$this->getEnvironment()->getEnvironmentVariable()})->render(); ?>";
        }

        return null;
    }

}