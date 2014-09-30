<?php namespace Anomaly\Lexicon\Conditional\Expression;

use Anomaly\Lexicon\Node\NodeType\Conditional;

/**
 * Class ValueNode
 *
 * @author Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package Anomaly\Lexicon\Conditional\Expression
 */
class ValueNode extends Conditional
{

    public function setup()
    {
        $this->setName($this->match(0));
    }

    public function compile()
    {
        return $this->getName();
    }

}