<?php namespace Aiws\Lexicon\Node;

use Aiws\Lexicon\Util\Type;

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

    public function getRegexMatcher()
    {
        return "/\{\{\s*(?!{$this->lexicon->getIgnoredMatchers()})({$this
            ->lexicon->getRegex()->getVariableRegexMatcher()})(\s+.*?)?\s*(\/)?\}\}/ms";
    }

    public function compile()
    {
        $attributes = $this->newAttributeParser()->compile();

        $finder = $this->getContextFinder();

        $expected = Type::ECHOABLE;

        $echo = $end = null;

        if (!$this->getIsEmbedded()) {
            $echo = 'echo ';
            $end = ';';
        }

        return "{$echo}\$__lexicon->get({$finder->getItemName()}, '{$finder->getName(
        )}', {$attributes}, '', null, '{$expected}'){$end}";
    }

    public function compileKey()
    {
        return $this->getCount();
    }

}