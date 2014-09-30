<?php namespace Anomaly\Lexicon\Conditional\Expression;

/**
 * Class BooleanTestNode
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package Anomaly\Lexicon\Conditional\Expression
 */
class BooleanTestNode extends ExpressionNode
{


    public function setup()
    {
        $this->setCurrentContent(trim($this->match(0)));
    }

    public function getOperators()
    {
        return $this->getLexicon()->getFoundation()->getConditionalHandler()->getTestOperators();
    }

    public function compile()
    {
        $children = $this->getChildren();
dd($children);
        return "\$__data['__env']->variable(\$__data,{$this->getName()})";
    }

}