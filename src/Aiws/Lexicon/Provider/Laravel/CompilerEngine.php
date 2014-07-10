<?php namespace Aiws\Lexicon\Provider\Laravel;

use Illuminate\View\Engines\CompilerEngine as BaseCompilerEngine;

class CompilerEngine extends BaseCompilerEngine
{
    /**
     * Refresh
     *
     * @var bool
     */
    protected $refresh = true;

    /**
     * Enable string parsing
     *
     * @var bool
     */
    protected $parse = false;

    /**
     * Set refresh property
     *
     * @param bool $refresh
     * @return $this
     */
    public function refresh($refresh = true)
    {
        $this->refresh = $refresh;
        return $this;
    }

    /**
     * Set the parse flag
     *
     * @param bool $parse
     * @return $this
     */
    public function parse($parse = true)
    {
        $this->parse = $parse;
        return $this;
    }

    /**
     * Get the evaluated contents of the view.
     *
     * @param  string $path
     * @param  array  $data
     * @return string
     */
    public function get($path, array $data = array())
    {
        $this->lastCompiled[] = $path;

        // If this given view has expired, which means it has simply been edited since
        // it was last compiled, we will re-compile the views so we can evaluate a
        // fresh copy of the view. We'll pass the compiler the path of the view.

        if ($this->parse and ($this->refresh or $this->compiler->isNotParsed($path))) {
            $this->compiler->parseString($path);
        } elseif (!$this->parse and ($this->refresh or $this->compiler->isExpired($path))) {
            $this->compiler->compile($path);
        }

        $compiled = $this->compiler->getCompiledPath($path);

        // Once we have the path to the compiled file, we will evaluate the paths with
        // typical PHP just like any other templates. We also keep a stack of views
        // which have been rendered for right exception messages to be generated.
        $results = $this->evaluatePath($compiled, $data);

        array_pop($this->lastCompiled);

        return $results;
    }

}