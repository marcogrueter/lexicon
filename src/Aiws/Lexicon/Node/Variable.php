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

        $dataSource = '$' . $this->getParent()->getItemName();

        $finder = $this->getContextFinder();

        if ($this->getParent()->isRoot() or $finder->isRootContextName()) {
            $this->setName(str_replace('data.','', $this->getName()));
            $dataSource = $this->getEnvironment()->getEnvironmentVariable();
        }

        $expected = Type::ECHOABLE;

        return "<?php echo \$__lexicon->get({$dataSource}, '{$this->getName()}', {$attributes}, '', '', '{$expected}'); ?>";
    }

}