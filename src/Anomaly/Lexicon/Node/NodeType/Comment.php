<?php namespace Anomaly\Lexicon\Node\NodeType;

class Comment extends SingleNull
{
    /**
     * Regex
     *
     * @return string
     */
    public function regex()
    {
        return '/\{\{--.*?--\}\}/s';
    }
}