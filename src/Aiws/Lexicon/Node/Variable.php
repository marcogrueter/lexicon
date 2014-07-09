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
        $attributes = var_export($this->getAttributes(), true);

        $finder = $this->getContextFinder();

        $expected = Type::ECHOABLE;

        return "<?php echo \$__lexicon->get({$finder->getItemName()}, '{$finder->getName()}', {$attributes}, '', '', '{$expected}'); ?>";
    }

}