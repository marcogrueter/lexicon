<?php namespace Anomaly\Lexicon\Node;

use Anomaly\Lexicon\Contract\Node\NodeInterface;
use Anomaly\Lexicon\Contract\Node\ValidatorInterface;

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

    public function isValid()
    {
        return true;
    }

    /**
     * @param $name = string
     * @return int
     */
    protected function getCount($name = '')
    {
        $count = 0;

        if ($parent = $this->node->getParent()) {
            foreach ($parent->getChildren() as $node) {
                /** @var NodeInterface $node */
                if ($node->getName() == $name) {
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
        return $this->getCount($name) == $this->getCount($otherName);
    }

    /**
     * Exists
     *
     * @param $name
     * @return bool
     */
    public function exists($name)
    {
        return (bool)$this->getCount($name);
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
                    $currentOrder = strpos($parent->getParsedContent(), $node->getExtractionId());
                } elseif ($node->getName() == $name) {
                    $otherOrder = strpos($parent->getParsedContent(), $node->getExtractionId());
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

}