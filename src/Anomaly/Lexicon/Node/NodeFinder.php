<?php namespace Anomaly\Lexicon\Node;

use Anomaly\Lexicon\Attribute\AttributeNode;
use Anomaly\Lexicon\Attribute\SplitterNode;
use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\Node\NodeInterface;
use Anomaly\Lexicon\Node\NodeType\Node;
use Anomaly\Lexicon\Stub\Node\NodeFinderStub;

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
     * Get node
     *
     * @return NodeInterface
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * Strip special prefixes and get the real the name
     *
     * @return string
     */
    public function getName()
    {
        $name = $this->getNode()->getName();

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

        $node = $this->getNode();

        if ($this->hasRootAliasPrefix()) {

            $source = '$__data';

        } elseif ($node instanceof AttributeNode) {

            if ($parent = $this->getAttributeNodeParent()) {
                $source = $parent->getItemSource();
            }

        } elseif (!$this->isChildOfRoot()) {

            if ($node = $this->getNodeByAlias()) {

                $source = $node->getItemSource();

            } elseif ($parent = $this->getParent()) {

                $source = $parent->getItemSource();

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
            $parts = explode($this->glue(), $this->getNode()->getName());
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
        $node = $this->getNode()->getParent();

        while ($node and $node->getItemAlias() !== $this->getAlias() and $node->getParent()) {
            $node = $node->getParent();
        }

        if ($node and $node->isRoot()) {
            $node = null;
        }

        return $node;
    }

    public function getAttributeNodeParent()
    {
        $node = $this->getNode();
        while (($node instanceof AttributeNode or $node instanceof SplitterNode) and $node->getParent()) {
            $node = $node->getParent();
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
        return starts_with($this->getNode()->getName(), $this->getRootStart());
    }

    /**
     * Has alias prefix
     *
     * @return bool
     */
    public function hasAliasPrefix()
    {
        return starts_with($this->getNode()->getName(), $this->glue());
    }

    /**
     * @return string
     */
    public function getRootStart()
    {
        return $this->glue() . $this->getLexicon()->getRootAlias() . $this->glue();
    }

    /**
     * @return LexiconInterface
     */
    public function getLexicon()
    {
        return $this->getNode()->getLexicon();
    }

    /**
     * @return NodeInterface
     */
    public function getParent()
    {
        return $this->getNode()->getParent();
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
        $name = $this->getNode()->getName();

        if ($this->hasAliasPrefix()) {
            $aliasPrefix = $this->getAliasPrefix();
            if (substr($name, 0, strlen($aliasPrefix)) == $aliasPrefix) {
                $name = substr($name, strlen($aliasPrefix));
            }
        }

        return $name;
    }

    /**
     * Stub for testing with PHPSpec
     *
     * @param Node $node
     * @return NodeFinder
     */
    public static function stub()
    {
        return NodeFinderStub::get();
    }

}