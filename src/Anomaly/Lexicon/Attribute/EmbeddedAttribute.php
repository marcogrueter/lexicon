<?php namespace Anomaly\Lexicon\Attribute;

use Anomaly\Lexicon\Contract\ExtractionInterface;

class EmbeddedAttribute implements ExtractionInterface
{
    protected $content;

    protected $name;

    protected $attributes;

    public function __construct($match)
    {
        $this->content   = isset($match[0]) ? $match[0] : '';
        $this->name       = isset($match[1]) ? $match[1] : '';
        $this->attributes = isset($match[2]) ? $match[2] : '';
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getId()
    {
        return md5($this->content);
    }

}