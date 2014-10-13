<?php namespace Anomaly\Lexicon\Conditional\Expression;

use Anomaly\Lexicon\Contract\Node\ConditionalInterface;
use Anomaly\Lexicon\Stub\LexiconStub;

/**
 * Class BooleanTestNode
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package Anomaly\Lexicon\Conditional\Expression
 */
class BooleanTestNode extends ExpressionNode
{

    /**
     * Get operators
     *
     * @return array
     */
    public function getOperators()
    {
        return $this->getConditionalHandler()->getTestOperators();
    }

    /**
     * Get operators
     *
     * @return array
     */
    public function regex()
    {
        return $this->getConditionalHandler()->getTestOperatorRegex();
    }

    /**
     * Create logical operator
     *
     * @param $operator
     * @return \Anomaly\Lexicon\Contract\Node\NodeInterface
     */
    public function getOperatorNodeType()
    {
        return new BooleanTestOperatorNode($this->getLexicon());
    }


    /**
     * Create boolean test node
     *
     * @param $booleanTest
     * @return \Anomaly\Lexicon\Contract\Node\NodeInterface
     */
    public function getExpressionNodeType()
    {
        return new ValueNode($this->getLexicon());
    }

    /**
     * Compile source
     *
     * @return string
     */
    public function compile()
    {
        $children = $this->getChildren()->values();

        if (count($children) == 3 and $children[1] instanceof BooleanTestOperatorNode) {
            /** @var ValueNode $left */
            $left = $children[0];
            /** @var BooleanTestOperatorNode $operator */
            $operator = $children[1];
            /** @var ValueNode $right */
            $right = $children[2];

            $source = "\$__data['__env']->booleanTest({$left->compile()},{$right->compile()},{$operator->compile()})";
        } else {
            /** @var ValueNode $valueNode */
            $valueNode = $this->createChildNodes()->getChildren()->first();
            $source = $valueNode->compile();
        }

        return $source;
    }

    /**
     * Stub for testing with PHPSpec
     *
     * @return ConditionalInterface
     */
    public static function stub()
    {
        $lexicon     = LexiconStub::get();
        $nodeFactory = $lexicon->getFoundation()->getNodeFactory();
        /** @var ConditionalInterface $conditionalNode */
        $expressionNode = $nodeFactory->make(new ExpressionNode($lexicon), []);
        return $nodeFactory->make(new static($lexicon), [], $expressionNode);
    }

}