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
    public function countSiblings($name = null)
    {
        $count = 0;

        /** @var NodeInterface $node */
        foreach ($this->getNode()->getSiblings() as $node) {
            if ($node->getName() == $name) {
                $count++;
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
        return count($this->getNode()->getSiblings()) > 0;
    }

    /**
     * Is after
     *
     * @param      $name
     * @param bool $strict
     * @return bool
     */
    public function isAfter($name, $strict = false)
    {
        if ($strict and !$this->hasSiblings($name)) {
            return false;
        }

        $siblingPosition = 0;

        if ($sibling = $this->getNode()->getFirstSibling($name)) {
            $siblingPosition = $sibling->getPosition();
        }

        return $this->getNode()->getPosition() > $siblingPosition;
    }

    /**
     * Is after existing
     *
     * @param $name
     * @return bool
     */
    public function isAfterExisting($name)
    {
        return $this->isAfter($name, true);
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
