<?php namespace Anomaly\Lexicon\Conditional\Expression;

use Anomaly\Lexicon\Contract\Node\ConditionalInterface;
use Anomaly\Lexicon\Node\NodeType\Conditional;
use Anomaly\Lexicon\Stub\LexiconStub;

/**
 * Class ExpressionNode
 *
 * @author  Osvaldo Brignoni <obrignoni@anomaly.is>
 * @package Anomaly\Lexicon\Conditional\Expression
 */
class ExpressionNode extends Conditional
{

    protected $extractable = false;

    /**
     * @return \Anomaly\Lexicon\Conditional\ConditionalHandler
     */
    public function getConditionalHandler()
    {
        return $this->getLexicon()->getFoundation()->getConditionalHandler();
    }

    /**
     * Get operators
     *
     * @return array
     */
    public function getOperators()
    {
        return $this->getConditionalHandler()->getLogicalOperators();
    }

    /**
     * Get regex
     *
     * @return string
     */
    public function regex()
    {
        return $this->getConditionalHandler()->getLogicalOperatorRegex();
    }

    /**
     * Get matches
     *
     * @return array
     */
    public function getMatches($string, $regex = null)
    {
        if (!$regex) {
            $regex = $this->regex();
        }

        return preg_split($regex, $string, -1, PREG_SPLIT_DELIM_CAPTURE);
    }

    /**
     * @return $this
     */
    public function createChildNodes()
    {
        $matches = $this->getMatches($this->getContent());
        foreach ($matches as $offset => $match) {
            $this->createChildNode($match, $offset);
        }
        return $this;
    }

    /**
     * @param $match
     * @param $offset
     */
    public function createChildNode($match, $offset)
    {
        $nodeFactory = $this->getNodeFactory();

        $nodeType = in_array($match, $this->getOperators())
            ? $this->getOperatorNodeType()
            : $this->getExpressionNodeType();

        $child = $nodeFactory
            ->make($nodeType, [], $this, $offset, $this->getDepth())
            ->setContent($match);

        $this->addChild($child);
    }

    /**
     * Create logical operator
     *
     * @param $operator
     * @return \Anomaly\Lexicon\Contract\Node\NodeInterface
     */
    public function getOperatorNodeType()
    {
        return new LogicalOperatorNode($this->getLexicon());
    }

    /**
     * Create boolean test node
     *
     * @param $booleanTest
     * @return \Anomaly\Lexicon\Contract\Node\NodeInterface
     */
    public function getExpressionNodeType()
    {
        return new BooleanTestNode($this->getLexicon());
    }

    /**
     * Compile expression
     *
     * @return string
     */
    public function compile()
    {
        $source = '';
        /** @var Conditional $node */
        foreach ($this->getChildren() as $node) {
            $source .= $node->compile();
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
        $conditionalNode = $nodeFactory->make(new Conditional($lexicon), []);
        $conditionalNode->setContent('foo or bar and baz && yin || yang');
        $expressionNode = $nodeFactory->make(new static($lexicon), [], $conditionalNode);
/*        $expressionNode
            ->setContent('foo or bar and baz && yin || yang')
            ->createChildNodes();*/
        return $expressionNode;
    }

}
