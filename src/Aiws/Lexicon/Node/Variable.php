<?php namespace Aiws\Lexicon\Node;

use Aiws\Lexicon\Util\Type;

class Variable extends Single
{
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

        return "echo \$__lexicon->get({$finder->getItemName()}, '{$finder->getName(
        )}', {$attributes}, '', null, '{$expected}');";
    }

}