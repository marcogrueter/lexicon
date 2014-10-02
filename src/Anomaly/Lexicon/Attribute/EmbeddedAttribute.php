<?php namespace Anomaly\Lexicon\Attribute;

use Anomaly\Lexicon\Stub\LexiconStub;

class EmbeddedAttribute extends AttributeNode
{

    public function setup()
    {
        $this->setContent($this->match(0));
        $this->setName($this->match(1));
        $this->setRawAttributes($this->match(2));
    }

    public static function stub()
    {
        return new static(LexiconStub::get());
    }

}