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
        $children = $this->createChildNodes()->getChildren();

        $parts = $children->values();

        if (count($parts) == 3 and $parts[1] instanceof BooleanTestOperatorNode) {
            /** @var ValueNode $left */
            $left = $parts[0];
            /** @var BooleanTestOperatorNode $operator */
            $operator = $parts[1];
            /** @var ValueNode $right */
            $right = $parts[2];

            $source = "\$this->booleanTest({$left->compile()},{$right->compile()},{$operator->compile()})";
        } else {
            /** @var ValueNode $valueNode */
            $valueNode = $children->first();
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