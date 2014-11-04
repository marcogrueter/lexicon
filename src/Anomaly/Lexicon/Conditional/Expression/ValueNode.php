<?php namespace Anomaly\Lexicon\Conditional\Expression;

use Anomaly\Lexicon\Node\NodeType\Conditional;
use Anomaly\Lexicon\Node\NodeType\Variable;

/**
 * Class ValueNode
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package Anomaly\Lexicon\Conditional\Expression
 */
class ValueNode extends ExpressionNode
{

    /**
     * Compile
     *
     * @return string
     */
    public function compile()
    {
        $this->setName($this->getContent());
        $finder = $this->getNodeFinder();
        $name = $finder->getName();
        $item = $finder->getItemSource();
        $source = null;
        if (!empty($name)) {
            $source = "\$this->variable({$item}, '{$name}')";
        }
        return $source;
    }

}