<?php namespace Anomaly\Lexicon\View;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\View\CompilerInterface;
use Anomaly\Lexicon\Contract\View\CompilerSequenceInterface;
use Anomaly\Lexicon\Stub\LexiconStub;
use Illuminate\View\Compilers\Compiler as BaseCompiler;

/**
 * Class Compiler
 *
 * @package Anomaly\Lexicon\View
 */
class Compiler extends BaseCompiler implements CompilerSequenceInterface
{
    /**
     * @var LexiconInterface
     */
    protected $lexicon;

    /**
     * @var
     */
    protected $hash;

    /**
     * Path
     *
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $sequence = [
        'Anomaly\Lexicon\View\LexiconCompiler'
    ];

    /**
     * Compiler interface
     *
     * @param $path
     * @return CompilerInterface
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set view hash
     *
     * @param $hash
     * @return CompilerInterface
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
        return $this;
    }

    /**
     * Get view hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param LexiconInterface $lexicon
     * @return CompilerInterface
     */
    public function setLexicon(LexiconInterface $lexicon)
    {
        $this->lexicon = $lexicon;
        return $this;
    }

    /**
     * @return LexiconInterface
     */
    public function getLexicon()
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
        $this->setPath($path);

        $compiledPath = $this->getCompiledPath($path);

        if ($this->getLexicon()->isStringTemplate($path)) {

            $this->compileFromString($path, $compiledPath);

        } else {

            $this->compileFromFile($path, $compiledPath);

        }

    }

    public function compileFromFile($path, $compiledPath)
    {
        $this->setHash(substr(strrchr($compiledPath, '/'), 1));

        $contents = $this->compileString($this->files->get($path));

        if (!is_null($this->cachePath)) {
            $this->files->put($compiledPath, $contents);
        }
    }

    /**
     * Compile the given string parse-able contents.
     *
     * @param $string
     * @internal param string $value
     * @return string
     */
    public function compileFromString($string, $compiledPath)
    {
        $this->setHash(substr(strrchr($compiledPath, '/'), 1));

        $contents = $this->compileString($string);

        if (!is_null($this->cachePath)) {
            $this->files->put($compiledPath, $contents);
        }
    }

    /**
     * Compile string
     *
     * @param string $string
     * @return string
     */
    public function compileString($string = '')
    {
        if (!$this->getLexicon()->isPhpAllowed()) {
            $string = $this->escapePhp($string);
        }

        /** @var CompilerInterface $compiler */
        foreach ($this->getCompilerSequence() as $compiler) {
            $string = $compiler->compile($string);
        }

        return $this->compileView($string);
    }

    /**
     * Get compiler sequence
     */
    public function getCompilerSequence()
    {
        $compilers = [];

        foreach ($this->getLexicon()->getFoundation()->getCompilerSequence() as $class) {
            $compilers[] = new $class($this->getLexicon());
        }

        return $compilers;
    }

    /**
     * Escape PHP
     *
     * @param $string
     * @return mixed
     */
    public function escapePhp($string)
    {
        return str_replace(array('<?', '?>'), array('&lt;?', '?&gt;'), $string);
    }

    /**
     * Determine if the view at the given path is expired.
     *
     * @param $string
     * @internal param string $path
     * @return bool
     */
    public function isNotParsed($string)
    {
        $isNotParsed = false;

        $compiled = $this->getCompiledPath($string);

        // If the compiled file doesn't exist we will indicate that the view is expired
        // so that it can be re-compiled. Else, we will verify the last modification
        // of the views is less than the modification times of the compiled views.
        if (!$this->cachePath || !$this->files->exists($compiled)) {
            $isNotParsed = true;
        }

        return $isNotParsed;
    }

    /**
     * Compile the view from the template
     *
     * @param string $source
     * @return string Compiled template
     */
    protected function compileView($source = '')
    {
        $data = [
            '[namespace]' => $this->getLexicon()->getCompiledViewNamespace(),
            '[class]'     => $this->getLexicon()->getCompiledViewClass($this->getHash()),
            '[source]'    => $source,
        ];

        return str_replace(array_keys($data), $data, $this->getViewTemplate());
    }

    /**
     * Is expired
     *
     * @param string $path
     * @return bool
     */
    public function isExpired($path)
    {
        $lexicon = $this->getLexicon();

        $foundation = $lexicon->getFoundation();

        if ($foundation->isDebug()) {
            return true;
        }

        if ($lexicon->isStringTemplate($path) and $this->isNotParsed($path)) {
            return true;
        }

        return parent::isExpired($path);
    }

    /**
     * Get view template
     *
     * @return string
     */
    public function getViewTemplate()
    {
        return file_get_contents($this->getLexicon()->getCompiledViewTemplatePath());
    }

    /**
     * Engine stub for PHPSpec unit test at spec\Anomaly\Lexicon\View\EngineSpec
     *
     * @return CompilerInterface
     */
    public static function stub()
    {
        return LexiconStub::compiler();
    }

}