<?php namespace Anomaly\Lexicon\Node;

class IgnoreBlock
{
    public function regex()
    {
        return '/^@(\{\{.*?\}\})/s';
    }
}