<?php namespace Aiws\Lexicon\Contract;

interface PluginInterface
{

    public function getPluginName();

    public function setContent($content);

    public function setAttributes(array $attributes);

    public function attribute($name, $default = null, $defaultOffset = 0);

}