<?php namespace Aiws\Lexicon\Node;

use Aiws\Lexicon\Data\Context;

class Variable extends Single
{
    public function getRegex()
    {
        return "/\{\{\s*(?!{$this->lexicon->getIgnoredMatchers()})({$this->getVariableRegex()})(\s.*?)\}\}/m";
    }

    public function compile()
    {
        // @todo = modify data with plugins

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

        if ($context->getDataReflection()->isString()) {
            return $context->getSource()->tagsEcho()->toString();
        }

        return null;
    }

}