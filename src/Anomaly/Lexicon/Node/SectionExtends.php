<?php namespace Anomaly\Lexicon\Node;



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
            $source = "echo \$__data['__env']->make({$attribute},{$this->getLexicon()->getLexiconVariable()})->render();";
            $rootNode->footer[] = $source;
        }

        return null;
    }

}