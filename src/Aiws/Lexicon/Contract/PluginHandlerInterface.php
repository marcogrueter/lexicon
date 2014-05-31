<?php namespace Aiws\Lexicon\Contract;

interface PluginHandlerInterface
{
    public function setEnvironment(EnvironmentInterface $lexicon);

    public function call($name, $attributes, $content);
}