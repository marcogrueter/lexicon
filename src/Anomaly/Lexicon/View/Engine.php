<?php namespace Anomaly\Lexicon\View;

use Anomaly\Lexicon\Contract\View\CompilerInterface;
use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\View\EngineInterface;
use Anomaly\Lexicon\Contract\View\ViewTemplateInterface;
use Illuminate\View\Engines\CompilerEngine;

class Engine extends CompilerEngine implements EngineInterface
{
    /**
     * Runtime cache
     *
     * @var array
     */
    protected $cache = [];

    /**
     * @var CompilerInterface
     */
    protected $compiler;

    /**
     * Get the evaluated contents of the view.
     *
     * @param  string $__path
     * @param  array  $__data
     * @return string
     */
    public function get($__path, array $__data = array())
    {
        $this->lastCompiled[] = $__path;

        /** @var LexiconInterface $lexicon */
        $lexicon = $this->getLexicon();

        $compiler = $this->getCompiler();

        // If this given view has expired, which means it has simply been edited since
        // it was last compiled, we will re-compile the views so we can evaluate a
        // fresh copy of the view. We'll pass the compiler the path of the view.

        if ($lexicon->isParsePath($__path) and ($lexicon->isDebug() or $compiler->isNotParsed($__path))) {
            $compiler->compileParse($__path);
        } elseif (!$lexicon->isParsePath($__path) and ($lexicon->isDebug() or $compiler->isExpired($__path))) {
            $compiler->compile($__path);
        }

        $compiled = $compiler->getCompiledPath($__path);

        // Once we have the path to the compiled file, we will evaluate the paths with
        // typical PHP just like any other templates. We also keep a stack of views
        // which have been rendered for right exception messages to be generated.
        $results = $this->evaluatePath($compiled, $__data);

        array_pop($this->lastCompiled);

        return $results;
    }

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

            // @todo - simplify
            $segments             = explode('/', $__path);
            $hash                 = $segments[count($segments) - 1];
            $viewClass            = $this->getLexicon()->getFullViewClass($hash);

            if (!isset($this->cache[$__path]) and !class_exists($viewClass)) {
                /** @var string $__path */
                include $__path;
                $this->cache[$__path] = new $viewClass;
            }

            if (isset($this->cache[$__path]) and $view = $this->cache[$__path]) {
                if ($view instanceof ViewTemplateInterface) {
                    $view->render($__data);
                } else {
                    // @todo throw exception - must implement interface
                }
            }

        } catch (\Exception $e) {
            $this->handleViewException($e, $obLevel);
        }

        return ltrim(ob_get_clean());
    }

    /**
     * @codeCoverageIgnore
     * @return LexiconInterface
     */
    public function getLexicon()
    {
        return $this->getCompiler()->getLexicon();
    }

    /**
     * @codeCoverageIgnore
     * @return CompilerInterface
     */
    public function getCompiler()
    {
        return $this->compiler;
    }
}