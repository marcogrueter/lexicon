<?php namespace Anomaly\Lexicon\Plugin;

/**
 * Class StubPlugin
 *
 * @package stub\Plugin
 */
class StubPlugin extends Plugin
{

    public function foo()
    {
        return 'FOO, BAR, BAZ!';
    }

    public function filterMd5()
    {
        return md5($this->getAttribute('value', 0, 'foo'));
    }

    public function parseUppercase()
    {
        return strtoupper($this->getAttribute('value', 0, 'foo'));
    }

}