<?php namespace Aiws\Lexicon\Util\Attribute;



class EmbeddedAttribute
{
    protected $original;

    protected $name;

    protected $attributes;

    public function __construct($match)
    {
        $this->original = isset($match[0]) ? $match[0] : '';
        $this->name = isset($match[1]) ? $match[1] : '';
        $this->attributes = isset($match[2]) ? $match[2] : '';
    }

    public function getOriginal()
    {
        return $this->original;
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
        return md5($this->original);
    }

}