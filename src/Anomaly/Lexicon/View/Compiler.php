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
     * @return string
     */
    public function compile($path = null)
    {
        $this->setPath($path);

        $compiledPath = $this->getCompiledPath($path);

        if ($this->getLexicon()->isStringTemplate($path)) {

            return $this->compileFromString($path, $compiledPath);

        } else {

            return $this->compileFromFile($path, $compiledPath);

        }

    }

    /**
     * @param $path
     * @param $compiledPath
     * @return string
     * @throws \Illuminate\Filesystem\FileNotFoundException
     */
    public function compileFromFile($path, $compiledPath)
    {
        $contents = $this->compileString($this->files->get($path));

        $this->put($contents, $compiledPath);

        return $contents;
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
        $contents = $this->compileString($string);

        $this->put($contents, $compiledPath);

        return $contents;
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
            $string = $compiler->compile($string, $this->getPath());
        }

        return $this->compileView($string);
    }

    /**
     * Write contents to the file
     *
     * @param $contents
     * @param $compiledPath
     */
    protected function put($contents, $compiledPath)
    {
        if (!is_null($this->cachePath)) {
            $this->files->put($compiledPath, $contents);
        }
    }

    /**
     * Get the path to the compiled version of a view.
     *
     * @param  string $path
     * @return string
     */
    public function getCompiledPath($path)
    {
        return $this->cachePath . '/' . $this->getLexicon()->getCompiledViewClassPrefix() . md5($path) . '.php';
    }

    /**
     * Get compiled view class name
     *
     * @param $hash
     * @return string
     */
    public function getViewClassFromCompiledPath($compiledPath)
    {
        return str_replace('.php', '', substr(strrchr($compiledPath, '/'), 1));
    }

    /**
     * Get compiler sequence
     */
    public function getCompilerSequence()
    {
        $compilers = [];

        foreach ($this->getLexicon()->getFoundation()->getCompilerSequence() as $class) {
            if (($compiler = new $class($this->getLexicon())) instanceof CompilerInterface) {
                $compilers[] = $compiler;
            }
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
    public function isNotCompiled($compiledPath)
    {
        $isNotCompiled = false;

        // If the compiled file doesn't exist we will indicate that the view is expired
        // so that it can be re-compiled. Else, we will verify the last modification
        // of the views is less than the modification times of the compiled views.
        if (!$this->cachePath || !$this->files->exists($compiledPath)) {
            $isNotCompiled = true;
        }

        return $isNotCompiled;
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
            '[class]'     => $this->getViewClassFromCompiledPath($this->getCompiledPath($this->getPath())),
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
        } elseif ($lexicon->isStringTemplate($path) and $this->isNotCompiled($path)) {
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