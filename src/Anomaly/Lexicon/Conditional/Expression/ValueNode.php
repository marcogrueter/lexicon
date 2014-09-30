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
    protected $extractable = false;

    public function setup()
    {
        $this->setContent($this->match(0));
    }

    /**
     * Compile
     *
     * @return string
     */
    public function compile()
    {
        // TODO: use value resolver
        return "\$__data['__env']->variable(\$__data, '{$this->getContent()}')";
    }

}