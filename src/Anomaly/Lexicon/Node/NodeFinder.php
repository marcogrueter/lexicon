<?php namespace Anomaly\Lexicon\Node;


use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\Node\NodeInterface;

/**
 * Class NodeFinder
 *
 * @package Anomaly\Lexicon\Node
 */
class NodeFinder
{
    /**
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
     * Strip special prefixes and get the real the name
     *
     * @return string
     */
    public function getName()
    {
        $string = $this->node->getName();
        $prefix = $this->getRootStart();

        if ($this->hasRootContextName()) {
            // Remove data. from name
            if (substr($string, 0, strlen($prefix)) == $prefix) {

                $string = substr($string, strlen($prefix));

            }

        } elseif ($prefix = $this->getPrefix() and $this->findLoopItemNode($prefix)) {

            $prefix .= $this->getLexicon()->getScopeGlue();

            if (substr($string, 0, strlen($prefix)) == $prefix) {

                $string = substr($string, strlen($prefix));

            }

        }

        return $string;
    }

    /**
     * Get item name
     *
     * @return string
     */
    public function getItemSource()
    {
        $source = '$__data';

        if ($this->hasRootContextName()) {

            $source = '$__data';

        } elseif (!$this->isChildOfRoot()) {

            if ($prefix = $this->getPrefix() and $node = $this->findLoopItemNode($prefix)) {

                $source = $node->getItemSource();

            } else {

                $source = $this->getParent()->getItemSource();

            }
        }

        return $source;
    }

    public function isChildOfRoot()
    {
        return ($parent = $this->getParent() and $parent->isRoot());
    }

    /**
     * Get prefix
     *
     * @return null
     */
    public function getPrefix()
    {
        $prefix = null;

        $parts = explode($this->getLexicon()->getScopeGlue(), $this->node->getName());

        if ($this->hasMultipleScopes() and !$this->hasRootContextName()) {
            $prefix = $parts[0];
        }

        return $prefix;
    }

    /**
     * Find loop item node
     *
     * @param $prefix
     * @return NodeInterface|null
     */
    public function findLoopItemNode($prefix)
    {
        $node = $this->node->getParent();

        while ($node and $node->getParent() and $node->getLoopItemName() !== $prefix) {

            $node = $node->getParent();

        }

        if ($node and $node->isRoot()) {

            $node = $this->node->getParent();

        }

        return $node;
    }

    /**
     * Is root context name
     *
     * @return bool
     */
    public function hasRootContextName()
    {
        return $this->hasAlternateContextName(
            $this->getLexicon()->getRootContextName() . $this->getLexicon()->getScopeGlue()
        );
    }

    public function hasAlternateContextName($start)
    {
        return ($this->hasMultipleScopes() and starts_with($this->node->getContextName(), $start));
    }

    public function hasMultipleScopes()
    {
        return str_contains($this->node->getContextName(), $this->getLexicon()->getScopeGlue());
    }

    public function getRootStart()
    {
        return $this->getLexicon()->getRootContextName() . $this->getLexicon()->getScopeGlue();
    }

    /**
     * @return LexiconInterface
     */
    public function getLexicon()
    {
        return $this->node->getLexicon();
    }

    /**
     * @return NodeInterface
     */
    public function getParent()
    {
        return $this->node->getParent();
    }
} 