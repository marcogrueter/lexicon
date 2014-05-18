<?php namespace Aiws\Lexicon\Contract;

interface EnvironmentInterface
{
    public function getScopeGlue();

    public function getMaxDepth();

    public function getNodeTypes();
}