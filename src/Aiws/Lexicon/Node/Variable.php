<?php namespace Aiws\Lexicon\Node;

class Variable extends Single
{
    public function getRegexMatcher()
    {
        return "/\{\{\s*(?!{$this->lexicon->getIgnoredMatchers()})({$this
            ->lexicon->getRegex()->getVariableRegexMatcher()})(\s+.*?)?\s*(\/)?\}\}/ms";
    }

    public function compile()
    {
        $attributes = var_export($this->attributes, true);

        $dataSource = '$' . $this->parent->getItem();

        if ($this->parent->isRoot()) {
            $dataSource = '$__data';
        }

        return "<?php echo \$__lexicon->getVariable({$dataSource}, '{$this->getName()}', {$attributes}); ?>";
    }

}