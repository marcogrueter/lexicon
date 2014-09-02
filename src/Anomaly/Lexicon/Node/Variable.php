<?php namespace Anomaly\Lexicon\Node;

use Anomaly\Lexicon\Expected;

class Variable extends Single
{
    protected $isEmbedded = false;

    public function setIsEmbedded($isEmbedded = false)
    {
        $this->isEmbedded = $isEmbedded;
        return $this;
    }

    public function getIsEmbedded()
    {
        return $this->isEmbedded;
    }

    public function regex()
    {
        return "/\{\{\s*({$this
            ->lexicon->getRegex()->getVariableRegexMatcher()})(\s+.*?)?\s*(\/)?\}\}/ms";
    }

    public function compile()
    {
        $attributes = $this->newAttributeParser()->compile();

        $finder = $this->getContextFinder();

        $expected = Expected::ECHOABLE;

        $echo = $end = null;

        if (!$this->getIsEmbedded()) {
            $echo = 'echo ';
            $end  = ';';
        }

        return "{$echo}\$this->view()->variable({$finder->getItemName()}, '{$finder->getName(
        )}', {$attributes}, '', null, '{$expected}'){$end}";
    }

    public function compileKey()
    {
        return $this->getCount();
    }

    /**
     * Compile a named key from an ordered embedded attribute
     *
     * @return string
     */
    public function compileNamedFromOrderedKey()
    {
        if (!$this->getIsEmbedded()) {

            $node = $this->make(['name' => $this->getName()], $this->getParent());

            $finder = $node->getContextFinder();

            return $finder->getName();
        }

        return $this->getName();
    }

}