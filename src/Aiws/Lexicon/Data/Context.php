<?php namespace Aiws\Lexicon\Data;

class Context
{
    protected $data;

    protected $name;

    protected $currentName;

    protected $key;

    protected $source;

    protected $segments = [];

    protected $position = 1;

    protected $isRoot = false;

    public function __construct($contextData, $name = null, $source = '', $isRoot = true)
    {
        $this->name   = $this->currentName = $name;
        $this->data   = $contextData;
        $this->source = new Source($source);
        $this->isRoot = $isRoot;

        $this->find();
    }

    public function find()
    {
        if (!empty($this->currentName)) {

            $this->segments = explode('.', $this->currentName);
            $this->key      = array_shift($this->segments);
            $this->data     = $this->findData();

            if (!empty($this->segments) and $this->data) {
                $this->position++;
                $this->currentName = implode('.', $this->segments);
                $this->find();
            }
        }
    }

    public function findData()
    {
        $reflection = new Reflection($this->data);

        if ($this->isRootKey() and $reflection->hasKey($this->key)) {
            $this->source->write("\${$this->key}");
        }

        if ($reflection->hasObjectKey($this->key)) {

            if (!$this->isRootKey()) {
                $this->source->write("->{$this->key}");
            }

            return $this->data->{$this->key};

        } elseif ($reflection->hasArrayKey($this->key)) {

            if (!$this->isRootKey()) {
                $this->source->write("['{$this->key}']");
            }

            return $this->data[$this->key];

        }

        return null;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getDataReflection()
    {
        return new Reflection($this->data);
    }

    public function getSource()
    {
        return $this->source;
    }

    public function toString()
    {
        return (string)$this->source;
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function isFirst()
    {
        return $this->position == 1;
    }

    public function isRootKey()
    {
        return ($this->isRootContext() and $this->isFirst());
    }

    public function isRootContext()
    {
        return $this->isRoot;
    }
}