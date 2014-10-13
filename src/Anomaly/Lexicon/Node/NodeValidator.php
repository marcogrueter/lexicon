<?php namespace Anomaly\Lexicon\Node;

use Anomaly\Lexicon\Contract\Node\NodeInterface;
use Anomaly\Lexicon\Contract\Node\ValidatorInterface;
use Anomaly\Lexicon\Stub\Node\NodeFinderStub;

class NodeValidator implements ValidatorInterface
{

    /**
     * Node
     *
     * @var NodeInterface
     */
    protected $node;

    /**
     * @param NodeInterface $node
     */
    public function __construct(NodeInterface $node)
    {
        $this->node = $node;
    }

    /**
     * Is valid
     *
     * @return bool
     */
    public function isValid()
    {
        return true;
    }

    /**
     * @param string $name
     * @return int
     */
    public function countSiblings($name)
    {
        $count = 0;
        /** @var NodeInterface $node */
        if ($siblings = $this->getNode()->getSiblings()) {
            foreach ($siblings as $node) {

                if (method_exists($node, 'getConstructName')) {
                    $nodeName = $node->getConstructName();
                } else {
                    $nodeName = $node->getName();
                }

                if ($nodeName == $name) {
                    $count++;
                }
            }
        }
        return $count;
    }

    /**
     * @param $name
     * @param $otherName
     * @return bool
     */
    public function isEqualCount($name, $otherName)
    {
        return $this->countSiblings($name) == $this->countSiblings($otherName);
    }

    /**
     * Exists
     *
     * @param $name
     * @return bool
     */
    public function hasSiblings($name)
    {
        $siblings = $this->getNode()->getSiblings();

        return count($siblings);
    }

    /**
     * Is after
     *
     * @param      $name
     * @param bool $strict
     * @return bool
     */
    public function isAfter($name)
    {
        $siblingPosition = ($sibling = $this->getNode()->getFirstSibling($name)) ? $sibling->getPosition() : 0;
        return $this->getNode()->getPosition() > $siblingPosition;
    }

    /**
     * Get node
     *
     * @return NodeInterface
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * Get node
     *
     * @return NodeInterface
     */
    public static function stub()
    {
        return new static(NodeFinderStub::get()->getNode());
    }

}
