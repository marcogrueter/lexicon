<?php namespace Aiws\Lexicon\Util\Attribute;

use Aiws\Lexicon\Node\Node;
use Aiws\Lexicon\Node\Variable;
use Aiws\Lexicon\Util\Type;

class AttributeNode extends Node
{
    protected $embedded;

    protected $key = '';

    protected $value = '';

    protected $isNamed = true;

    public function getSetup(array $match)
    {
        $this
            ->setKey(isset($match[1]) ? $match[1] : null)
            ->setValue(isset($match[3]) ? $match[3] : '');
    }

    public function getRegexMatcher($embedded = false)
    {
        return '/(.*?)\s*=(\'|"|&#?\w+;)(.*?)(?<!\\\\)\2/ms';
    }

    public function getMatches($string)
    {
        return $this->lexicon->getRegex()->getMatches($string, $this->getRegexMatcher());
    }

    public function setEmbedded(EmbeddedAttribute $embedded = null)
    {
        $this->embedded = $embedded;
        return $this;
    }

    public function setKey($key = '')
    {
        $this->key = $key;
        return $this;
    }

    public function getKey()
    {
        return trim($this->key);
    }

    public function setValue($value = '')
    {
        $this->value = $value;
        return $this;
    }

    public function getValue()
    {
        return trim($this->value);
    }

    public function getEmbeddedId()
    {
        return $this->getValue();
    }

    public function getEmbedded()
    {
        return $this->embedded;
    }

    public function compileKey()
    {
        return $this->getKey();
    }

    public function compileSharedKey()
    {
        if (!$this->isNamed and $this->getEmbedded()) {

            $node = $this->newVariableNode()->make(['name' => $this->getEmbedded()->getName()], $this->getParent());

            $finder = $node->getContextFinder();

            return $finder->getName();
        }

        return $this->getKey();
    }

    public function newVariableNode()
    {
        $node = new Variable();

        $node->setEnvironment($this->getEnvironment());

        return $node;
    }

    public function compileEmbedded()
    {
        $node = $this->newVariableNode()->make(['name' => $this->getEmbedded()->getName()], $this->getParent());

        $finder = $node->getContextFinder();

        return "\$__lexicon->get({$finder->getItemName()},'{$finder->getName()}', [])";
    }

    public function compileLiteral()
    {
        return "'{$this->getValue()}'";
    }

    public function compile()
    {
        if ($this->getEmbedded()) {
            return $this->compileEmbedded();
        } else {
            return $this->compileLiteral();
        }
    }
}