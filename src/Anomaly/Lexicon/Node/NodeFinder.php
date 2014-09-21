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
        $name = $this->node->getName();

        if ($this->hasAliasPrefix()) {
            $name = $this->getCleanName();
        }

        return $name;
    }

    /**
     * Get item name
     *
     * @return string
     */
    public function getItemSource()
    {
        $source = '$__data';

        if ($this->hasRootAliasPrefix()) {

            $source = '$__data';

        } elseif (!$this->isChildOfRoot()) {

            if ($node = $this->getNodeByAlias()) {

                $source = $node->getItemSource();

            } else {

                $source = $this->getParent()->getItemSource();

            }
        }

        return $source;
    }

    /**
     * The node is child of the root node
     *
     * @return bool
     */
    public function isChildOfRoot()
    {
        return ($parent = $this->getParent() and $parent->isRoot());
    }

    /**
     * Get alias
     *
     * @return null
     */
    public function getAlias()
    {
        $alias = null;
        if ($this->hasAliasPrefix()) {
            $parts = explode($this->glue(), $this->node->getName());
            $alias = $parts[1];
        }

        return $alias;
    }

    /**
     * Get alias prefix
     *
     * @return null
     */
    public function getAliasPrefix()
    {
        $aliasPrefix = null;

        if ($this->hasAliasPrefix()) {
            $aliasPrefix = $this->glue() . $this->getAlias() . $this->glue();
        }

        return $aliasPrefix;
    }

    /**
     * Find loop item node
     *
     * @param $alias
     * @return NodeInterface|null
     */
    public function getNodeByAlias()
    {
        $node = $this->node->getParent();

        while ($node and $node->getParent() and $node->getLoopItemName() !== $this->getAlias()) {

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
    public function hasRootAliasPrefix()
    {
        return starts_with($this->node->getName(), $this->getRootStart());
    }

    /**
     * Has alias prefix
     *
     * @return bool
     */
    public function hasAliasPrefix()
    {
        return starts_with($this->node->getName(), $this->glue());
    }

    /**
     * @return string
     */
    public function getRootStart()
    {
        return $this->glue() . $this->getLexicon()->getRootContextName() . $this->glue();
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

    /**
     * Alias for scope glue
     *
     * @return string
     */
    public function glue()
    {
        return $this->getLexicon()->getScopeGlue();
    }

    /**
     * Returns the node name without the alias prefix
     */
    public function getCleanName()
    {
        $name = $this->node->getName();

        if ($this->hasAliasPrefix()) {
            $aliasPrefix = $this->getAliasPrefix();
            if (substr($name, 0, strlen($aliasPrefix)) == $aliasPrefix) {
                $name = substr($name, strlen($aliasPrefix));
            }
        }

        return $name;
    }
} 