<?php namespace Aiws\Lexicon\Contract;

interface EnvironmentInterface
{
    public function getPluginHandler();

    public function getScopeGlue();

    public function getMaxDepth();

    public function getNodeTypes();

    public function getPlugin($name);
}