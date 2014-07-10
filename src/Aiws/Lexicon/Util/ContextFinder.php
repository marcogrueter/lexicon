<?php namespace Aiws\Lexicon\Util;

use Aiws\Lexicon\Node\Block;
use Aiws\Lexicon\Node\Node;
use Aiws\Lexicon\Node\Variable;

class ContextFinder
{

    /**
     * @var Node
     */
    protected $node;

    /**
     * Lexicon environment
     *
     * @var \Aiws\Lexicon\Contract\EnvironmentInterface
     */
    protected $lexicon;

    protected $parent;

    /**
     * @param Node $node
     */
    public function __construct(Node $node)
    {
        $this->node = $node;
        $this->lexicon = $this->node->getEnvironment();
        $this->parent = $this->node->getParent();
    }

    public function getName()
    {
        $string = $this->node->getName();
        $prefix = $this->getRootStart();

        if (substr($string, 0, strlen($prefix)) == $prefix) {
            $string = substr($string, strlen($prefix));
        }

        return $string;
    }

    public function getItemName()
    {
        //if ($this->node instanceof Variable) {
            if (($this->parent and $this->parent->isRoot()) or $this->isRootContextName()) {
                return $this->lexicon->getEnvironmentVariable();
            } elseif ($this->parent and !$this->parent->isRoot()) {
                return '$'.$this->parent->getItemName();
            } else {
                return $this->lexicon->getEnvironmentVariable();
            }
        //}
    }

    public function getLoopItemSource()
    {
        if ($this->node instanceof Block) {
            if (($this->parent and $this->parent->isRoot()) or $this->isRootContextName()) {
                return $this->lexicon->getEnvironmentVariable();
            } elseif ($this->parent and !$this->parent->isRoot()) {
                return '$'.$this->parent->getItemName();
            } else {
                return $this->lexicon->getEnvironmentVariable();
            }
        }
    }

    public function isRootContextName()
    {
        return $this->isAlternateContextName($this->lexicon->getRootContextName().$this->lexicon->getScopeGlue());
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
        return $this->lexicon->getRootContextName().$this->lexicon->getScopeGlue();
    }

}