<?php namespace Anomaly\Lexicon\View\Compiler;

use Anomaly\Lexicon\Contract\EnvironmentInterface;
use Anomaly\Lexicon\Contract\NodeBlockInterface;
use Illuminate\View\Compilers\Compiler as BaseCompiler;
use Illuminate\View\Compilers\CompilerInterface;

class Compiler extends BaseCompiler implements CompilerInterface
{
    /**
     * @var EnvironmentInterface
     */
    protected $lexicon;

    protected $hash;

    protected $streamCompiler;

    public function setHash($hash)
    {
        $this->hash = $hash;
        return $this;
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function setLexicon($lexicon)
    {
        $this->lexicon = $lexicon;
        return $this;
    }

    /**
     * @return EnvironmentInterface
     */
    public function getLexicon()
    {
        return $this->lexicon;
    }

    /**
     * Set stream compiler
     *
     * @param StreamCompiler $streamCompiler
     * @return $this
     */
    public function setStreamCompiler(StreamCompiler $streamCompiler)
    {
        $this->streamCompiler = $streamCompiler;
        return $this;
    }

    /**
     * @return StreamCompiler
     */
    public function getStreamCompiler()
    {
        return $this->streamCompiler;
    }

    /**
     * Compile the view at the given path.
     *
     * @param  string $path
     * @return void
     */
    public function compile($path = null)
    {
        $compiledPath = $this->getCompiledPath($path);

        $segments = explode('/', $compiledPath);

        $this->setHash($segments[count($segments) - 1]);

        $contents = $this->compileString($this->files->get($path));

        if (!is_null($this->cachePath)) {
            $this->files->put($this->getCompiledPath($path), $contents);
        }
    }

    public function compileString($content)
    {
        if (empty($content)) {
            return null;
        }

        if (!$this->getLexicon()->allowPhp()) {
            $content = $this->escapePhp($content);
        }

        //$noParse = $this->getLexicon()->getRegex()->extractNoParse($content);

        //$content = $noParse['content'];

        //$this->noParseExtractions = $noParse['extractions'];

        return $this->compileView(
            $this->getLexicon()->getBlockNodeType()->make(
                array(
                    'name'    => 'root',
                    'content' => $content,
                )
            )
        );
    }

    /**
     * Compile view
     *
     * @param NodeBlockInterface $node
     * @return string
     */
    public function compileView(NodeBlockInterface $block)
    {
        return (new ViewCompiler($this->setStreamCompiler(new StreamCompiler($block))))->compile();
    }

    /**
     * Compile the given string parse-able contents.
     *
     * @param  string $value
     * @return string
     */
    public function compileParse($content)
    {
        $this->setHash(str_replace('/', '', strrchr($this->getCompiledPath($content), '/')));

        $contents = $this->compileString($content);

        if (!is_null($this->cachePath)) {
            $this->files->put($this->getCompiledPath($content), $contents);
        }
    }

    /**
     * Escape PHP
     *
     * @param $content
     * @return mixed
     */
    public function escapePhp($content)
    {
        return str_replace(array('<?', '?>'), array('&lt;?', '?&gt;'), $content);
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
        if (!$this->cachePath || !$this->files->exists($compiled)) {
            return true;
        }

        return false;
    }

}