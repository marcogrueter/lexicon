<?php namespace Anomaly\Lexicon;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\Node\NodeInterface;
use Anomaly\Lexicon\Contract\Node\RootInterface;

class ContextFinder
{

    /**
     * @var NodeInterface
     */
    protected $node;

    /**
     * Lexicon environment
     *
     * @var LexiconInterface
     */
    protected $lexicon;

    /**
     * @var NodeInterface
     */
    protected $parent;

    /**
     * @param NodeInterface $node
     */
    public function __construct(NodeInterface $node)
    {
        $this->node    = $node;
        $this->lexicon = $this->node->getLexicon();
        $this->parent  = $this->node->getParent();
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

        if (substr($string, 0, strlen($prefix)) == $prefix) {
            $string = substr($string, strlen($prefix));
        }

        $prefix = $this->getPrefix() . $this->lexicon->getScopeGlue();

        if ($this->getPrefix() and $this->findLoopItemNode($this->getPrefix())) {
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
    public function getItemName()
    {

        if (($this->parent and $this->parent->isRoot()) or $this->isRootContextName()) {
            return '$__data';
        } elseif ($prefix = $this->getPrefix() and $node = $this->findLoopItemNode($prefix)) {
            return '$' . $node->getItemName();
        } elseif ($this->parent and !$this->parent->isRoot()) {
            return '$' . $this->parent->getItemName();
        } else {
            return '$__data';
        }

    }

    /**
     * Get prefix
     *
     * @return null
     */
    public function getPrefix()
    {
        $name = $this->node->getName();

        $nameSegments = explode($this->lexicon->getScopeGlue(), $name);

        $prefix = null;

        if (count($nameSegments) > 1 and !$this->isRootContextName()) {
            $prefix = $nameSegments[0];
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
        if ($node = $this->node->getParent()) {
            while ($node and $node->getLoopItemName() !== $prefix) {
                $node = $node->getParent();
            }
            if ($node and $node->isRoot()) {
                return null;
            }
        }

        return $node;
    }

    /**
     * Is root context name
     *
     * @return bool
     */
    public function isRootContextName()
    {
        return $this->isAlternateContextName($this->lexicon->getRootContextName() . $this->lexicon->getScopeGlue());
    }


    public function isAlternateContextName($start)
    {
        return $this->hasMultipleScopes() and starts_with($this->node->getContextName(), $start);
    }

    public function hasMultipleScopes()
    {
        $segments = explode('.', $this->node->getContextName());

        return count($segments) > 1;
    }

    public function getRootStart()
    {
        return $this->lexicon->getRootContextName() . $this->lexicon->getScopeGlue();
    }

}