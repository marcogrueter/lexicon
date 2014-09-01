<?php namespace Anomaly\Lexicon\Node;

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