<?php namespace Anomaly\Lexicon\Node;

class Comment extends Single
{
    public function getRegexMatcher()
    {
        return '/\{\{--.*?--\}\}/s';
    }

    public function compile()
    {
        return null;
    }
}