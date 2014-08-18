<?php namespace Aiws\Lexicon\Node;



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

        $attribute = $this->newAttributeParser()->compileAttribute('layout');

        if (!empty($attribute)) {
            $source = "echo \$__env->make({$attribute},{$this->getEnvironment()->getEnvironmentVariable()})->render();";
            $rootNode->footer[] = $source;
        }

        return null;
    }

}