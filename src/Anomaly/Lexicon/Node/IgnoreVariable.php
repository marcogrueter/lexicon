<?php namespace Anomaly\Lexicon\Node;

class IgnoreVariable
{
    public function regex()
    {
        return '/^@(\{\{.*?\}\})/s';
    }
}