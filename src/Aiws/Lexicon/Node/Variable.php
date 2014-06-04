<?php namespace Aiws\Lexicon\Node;

use Aiws\Lexicon\Data\Context;
use Aiws\Lexicon\Data\Reflection;

class Variable extends Single
{
    public function getRegex()
    {
        return "/\{\{\s*(?!{$this->lexicon->getIgnoredMatchers()})({$this->getVariableRegex()})(\s+.*?)?\s*(\/)?\}\}/ms";
    }

    public function compile()
    {
        // @todo = modify data with plugins

        if ($plugin = $this->lexicon->getPlugin($this->name)) {

            $value = $this->lexicon->call($this->name, $this->callbackParameters);

            $reflection = new Reflection($value);

            if ($reflection->isEchoable()) {
                $parameters = var_export($this->callbackParameters, true);
                return "<?php echo \$__lexicon->call('{$this->name}', {$parameters}); ?>";
            }

        } else {

            if ($parentIsRoot = !$this->parent->isRoot()) {
                $context = new Context(
                    $this->data,
                    $this->name,
                    '$' . $this->parent->getItem(),
                    $this->isRoot()
                );
            } else {
                $context = new Context(
                    $this->data,
                    $this->name
                );
            }

            if ($context->getDataReflection()->isEchoable()) {
                return $context->getSource()->tagsEcho()->toString();
            }

        }



        return null;
    }

}