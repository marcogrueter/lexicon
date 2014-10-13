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
/*        $parent = $this->getParent();

        while($parent instanceof Conditional and $this->getParent()) {
            $parent = $this->getParent();
        }


        $finder = $parent->getNodeFinder();

        $finder->getItemSource();*/

        $name = trim($this->getContent());
        $source = null;
        if (!empty($name)) {
            $source = "\$__data['__env']->variable(\$__data, '{$name}')";
        }
        return $source;
    }

}