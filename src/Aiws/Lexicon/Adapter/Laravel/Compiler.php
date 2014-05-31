<?php namespace Aiws\Lexicon\Adapter\Laravel;

use Aiws\Lexicon\Contract\EnvironmentInterface;
use Illuminate\View\Compilers\BladeCompiler;

class Compiler extends BladeCompiler
{
    /**
     * @var
     */
    protected $view;

    protected $lexicon;

    /**
     * All of the available compiler functions.
     *
     * @var array
     */
    protected $compilers = array(
        'Extensions',
        'Extends',
        //'Comments',
        //'Echos',
        //'Openings',
        //'Closings',
        //'Else',
        //'Unless',
        //'EndUnless',
        //'Includes',
        //'Each',
        //'Yields',
        //'Shows',
        //'Language',
        //'SectionStart',
        //'SectionStop',
        //'SectionAppend',
        //'SectionOverwrite',
    );

    public function setView($view)
    {
        $this->view = $view;
    }

    public function getData()
    {
        return $this->view->getData();
    }

    public function setEnvironment(EnvironmentInterface $lexicon)
    {
        $this->lexicon = $lexicon;
    }

    public function boot($lexicon)
    {
        $this->lexicon = $lexicon;

        $this->extend(
            function ($content) use ($lexicon) {
                return $this->lexicon->compile($content, $this->getData());
            }
        );

        return $this;
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

        $contents = $this->lexicon->compile($this->files->get($this->getPath()), $this->getData());

        if (!is_null($this->cachePath)) {
            $this->files->put($this->getCompiledPath($this->getPath()), $contents);
        }
    }

    /**
     * Get the path currently being compiled.
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
        $contents = $this->lexicon->compile($content, $this->getData());

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