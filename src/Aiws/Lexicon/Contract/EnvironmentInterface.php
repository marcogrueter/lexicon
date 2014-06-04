<?php namespace Aiws\Lexicon\Contract;

interface EnvironmentInterface
{
    public function getPluginHandler();

    public function getScopeGlue();

    public function getMaxDepth();

    public function getNodeTypes();

    public function getIgnoredMatchers();

    public function call($name, $attributes = [], $content = '');
}