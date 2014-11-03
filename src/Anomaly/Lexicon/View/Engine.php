<?php namespace Anomaly\Lexicon\View;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\View\CompilerInterface;
use Anomaly\Lexicon\Contract\View\EngineInterface;
use Anomaly\Lexicon\Stub\LexiconStub;
use Illuminate\View\Engines\CompilerEngine;

class Engine extends CompilerEngine implements EngineInterface
{

    /**
     * @var CompilerInterface
     */
    protected $compiler;

    /**
     * Get the evaluated contents of the view at the given path.
     *
     * @param  string $__path
     * @param  array  $__data
     * @return string
     */
    protected function evaluatePath($__path, $__data)
    {
        $obLevel = ob_get_level();

        ob_start();

        // We'll evaluate the contents of the view inside a try/catch block so we can
        // flush out any stray output that might get out before an error occurs or
        // an exception is thrown. This prevents any partial views from leaking.
        try {

            $lexicon      = $this->getLexicon();
            $viewClass    = $lexicon->getCompiledViewNamespace() . '\\' .
                $this->getCompiler()->getViewClassFromCompiledPath($__path);
            $compiledView = new $viewClass($lexicon);
            $compiledView->render($__data);

        } catch (\Exception $e) {
            $this->handleViewException($e, $obLevel);
        }

        return ltrim(ob_get_clean());
    }

    /**
     * @return LexiconInterface
     */
    public function getLexicon()
    {
        return $this->getCompiler()->getLexicon();
    }

    /**
     * @return CompilerInterface
     */
    public function getCompiler()
    {
        return $this->compiler;
    }

    /**
     * Engine stub for PHPSpec unit test at spec\Anomaly\Lexicon\View\EngineSpec
     *
     * @return Engine
     */
    public static function stub()
    {
        return LexiconStub::engine();
    }
}