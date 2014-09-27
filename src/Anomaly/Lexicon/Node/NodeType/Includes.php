<?php namespace Anomaly\Lexicon\Node\NodeType;


class Includes extends Single
{

    /**
     * Node name
     *
     * @var string
     */
    public $name = 'include';

    /**
     * Compile source
     *
     * @return null|string
     */
    public function compile()
    {
        $partial = $this->compileAttributeValue('partial');

        $source = null;

        if (!empty($partial)) {
            $share   = $this->getSharedAttributes();
            $source = "echo \$__data['__env']->make({$partial},array_merge(\$__data,{$share}))->render();";
        }

        return $source;
    }

    /**
     * Get shared attributes
     *
     * @return array
     */
    public function getSharedAttributes()
    {
        $sharedAttributes = [];

        if ($share   = $this->compileAttributeValue('share')) {
            foreach($this->getMatches($share, "/({$this->getVariableRegex()})/s") as $match) {
                dd($match);
            }
        }

        return $sharedAttributes;
    }

}