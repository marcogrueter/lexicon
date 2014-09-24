<?php namespace Anomaly\Lexicon\Contract\View;

use Illuminate\View\Engines\EngineInterface as BaseEngineInterface;

interface EngineInterface extends BaseEngineInterface
{
    /**
     * @return CompilerInterface
     */
    public function getCompiler();
}