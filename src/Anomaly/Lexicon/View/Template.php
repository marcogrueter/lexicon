<?php namespace Anomaly\Lexicon\View;

use Illuminate\View\Factory;

class Template
{

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view()
    {
        return $this->data['__env'];
    }

    public function render()
    {
    }
}