<?php namespace Anomaly\Lexicon\Attribute;

use Anomaly\Lexicon\Contract\Attribute\NodeInterface;

class NamedAttribute extends AttributeNode
{

    public function setup()
    {
        $this->setKey($this->match(1));
        $this->setValue($this->match(3));
        $this->setContent($this->match(3));
    }

    /**
     * Regex
     *
     * @return string
     */
    public function regex()
    {
        return '/(.*?)\s*=\s*(\'|"|&#?\w+;)(.*?)(?<!\\\\)\2/ms';
    }

    /**
     * Compile value
     *
     * @return string
     */
    public function compileValue()
    {
        $splitter = $this->getNodeFactory()->make(new SplitterNode($this->getLexicon()), [], $this);
        $splitter->setContent($this->getValue());
        $value = $splitter->createChildNodes()->compile();
        return $value;
    }

}