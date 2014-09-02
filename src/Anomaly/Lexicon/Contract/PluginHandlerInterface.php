<?php namespace Anomaly\Lexicon\Contract;

interface PluginHandlerInterface
{
    public function setEnvironment(LexiconInterface $lexicon);

    public function register($name, $class);

    public function get($name);

    public function call($name, $attributes = [], $content = '');
}