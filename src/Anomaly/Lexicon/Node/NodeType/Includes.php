<?php namespace Anomaly\Lexicon\Node\NodeType;


use Anomaly\Lexicon\Stub\LexiconStub;
use Anomaly\Lexicon\Support\ValueResolver;

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
        if ($share   = $this->compileAttributeValue('share', 1)) {
            $resolver = new ValueResolver();
            $names = explode(',', $resolver->removeQuotes($share));
            foreach($names as $name) {
                $node = $this->getNodeFactory()
                    ->make(new Variable($this->getLexicon()), [], $this)
                    ->setName($name);
                $sharedAttributes[] = "'{$name}'=>{$node->compile(false)}";
            }
        }
        return '['.implode(',',$sharedAttributes).']';
    }

    public static function stub()
    {
        $lexicon = LexiconStub::get();
        $factory = $lexicon->getFoundation()->getNodeFactory();
        return $factory->make(new static($lexicon));
    }

}