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
    public function countSiblingsWithName($name = null)
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
        return $this->countSiblingsWithName($name) == $this->countSiblingsWithName($otherName);
    }

    /**
     * Exists
     *
     * @param $name
     * @return bool
     */
    public function exists($name)
    {
        return $this->countSiblingsWithName($name) > 0;
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
        if ($strict and !$this->exists($name)) {
            return false;
        }

        $currentOrder = 0;
        $otherOrder   = 0;

        /** @var $parent NodeInterface */
        if ($parent = $this->node->getParent()) {
            foreach ($parent->getChildren() as $node) {
                /** @var NodeInterface $node */
                if ($this->node->getId() == $node->getId()) {
                    $currentOrder = strpos($parent->getCurrentContent(), $node->getExtractionId());
                } elseif ($node->getName() == $name) {
                    $otherOrder = strpos($parent->getCurrentContent(), $node->getExtractionId());
                }
            }
        }

        return $currentOrder > $otherOrder;
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
