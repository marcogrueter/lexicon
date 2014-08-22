<?php namespace Anomaly\Lexicon\Provider\Laravel;

use Anomaly\Lexicon\Contract\EnvironmentInterface;
use Illuminate\View\Compilers\Compiler;
use Illuminate\View\Compilers\CompilerInterface;

class LexiconCompiler extends Compiler implements CompilerInterface
{

    protected $lexicon;

    public function setEnvironment(EnvironmentInterface $lexicon)
    {
        $this->lexicon = $lexicon;
    }

    public function getEnvironment()
    {
        return $this->lexicon;
    }

    /**
     * Compile the view at the given path.
     *
     * @param  string $path
     * @return void
     */
    public function compile($path = null)
    {
        if ($path) {
            $this->setPath($path);
        }

        $compiledPath = $this->getCompiledPath($this->getPath());

        $segments = explode('/', $compiledPath);

        $this->lexicon->setCompiledView($segments[count($segments)-1]);

        $contents = $this->lexicon->compile($this->files->get($this->getPath()));

        if (!is_null($this->cachePath)) {
            $this->files->put($this->getCompiledPath($this->getPath()), $contents);
        }
    }

    /**
     * Get the path currently being compiled.
     *
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the path currently being compiled.
     *
     * @param string $path
     * @return void
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Compile the given Blade template contents.
     *
     * @param  string $value
     * @return string
     */
    public function parseString($content)
    {
        $this->lexicon->setCompiledView(str_replace('/', '',strrchr($this->getCompiledPath($content), '/')));

        $contents = $this->lexicon->compile($content);

        if (!is_null($this->cachePath)) {
            $this->files->put($this->getCompiledPath($content), $contents);
        }
    }

    public function getLexicon()
    {
        return $this->lexicon;
    }

    /**
     * Determine if the view at the given path is expired.
     *
     * @param  string $path
     * @return bool
     */
    public function isNotParsed($content)
    {
        $compiled = $this->getCompiledPath($content);

        // If the compiled file doesn't exist we will indicate that the view is expired
        // so that it can be re-compiled. Else, we will verify the last modification
        // of the views is less than the modification times of the compiled views.
        if (! $this->cachePath || !$this->files->exists($compiled)) {
            return true;
        }

        return false;
    }

}