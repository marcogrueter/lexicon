<?php namespace Aiws\Lexicon\Util;

use Aiws\Lexicon\Node\Node;

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

    /**
     * @param Node $node
     */
    public function __construct(Node $node)
    {
        $this->node = $node;
        $this->lexicon = $this->node->getEnvironment();
    }

    public function getName()
    {
        if ($this->isRootContextName()) {
            return str_replace($this->getRootStart(),'', $this->node->getName());
        }
    }

    public function getItemName()
    {
        if ($this->isRootContextName()) {
            return $this->lexicon->getEnvironmentVariable();
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