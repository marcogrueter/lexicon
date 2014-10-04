<?php namespace Anomaly\Lexicon\Conditional\Expression;

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
        $name = trim($this->getContent());
        $source = null;
        if (!empty($name)) {
            $source = "\$__data['__env']->variable(\$__data, '{$name}')";
        }
        return $source;
    }

}