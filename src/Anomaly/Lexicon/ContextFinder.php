<?php namespace Anomaly\Lexicon;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\NodeBlockInterface;
use Anomaly\Lexicon\Contract\NodeInterface;
use Anomaly\Lexicon\Node\Node;

class ContextFinder
{

    /**
     * @var Node
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
     * @param Node $node
     */
    public function __construct(NodeInterface $node)
    {
        $this->node    = $node;
        $this->lexicon = $this->node->getLexicon();
        $this->parent  = $this->node->getParent();
    }

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

    public function getLoopItemSource()
    {
        if ($this->node instanceof NodeBlockInterface) {
            if (($this->parent and $this->parent->isRoot()) or $this->isRootContextName()) {
                return '$__data';
            } elseif ($this->parent and !$this->parent->isRoot()) {
                return '$' . $this->parent->getItemName();
            } else {
                return '$__data';
            }
        }

        return null;
    }

    public function isRootContextName()
    {
        return $this->isAlternateContextName($this->lexicon->getRootContextName() . $this->lexicon->getScopeGlue());
    }

    public function isAlternateContextName($start)
    {
        return $this->hasMultipleScopes() and $this->startsWith($this->node->getContextName(), $start);
    }

    public function hasMultipleScopes()
    {
        $segments = explode('.', $this->node->getContextName());

        return count($segments) > 1;
    }

    public function contains($haystack, $needle = '.')
    {
        return (strpos($haystack, $needle) !== false);
    }

    public function startsWith($haystack, $needle)
    {
        return (strpos($haystack, $needle) === 0);
    }

    public function getRootStart()
    {
        return $this->lexicon->getRootContextName() . $this->lexicon->getScopeGlue();
    }

}