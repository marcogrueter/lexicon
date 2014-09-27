<?php namespace Anomaly\Lexicon\View;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\View\CompilerInterface;
use Anomaly\Lexicon\Node\NodeFactory;
use Anomaly\Lexicon\Stub\LexiconStub;
use Illuminate\View\Compilers\Compiler as BaseCompiler;

class Compiler extends BaseCompiler implements CompilerInterface
{
    /**
     * @var LexiconInterface
     */
    protected $lexicon;

    protected $hash;

    /**
     * Path
     *
     * @var string
     */
    protected $path;

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

        $this->setHash(substr(strrchr($compiledPath, '/'), 1));

        $nodeGroup = $this->getLexicon()->getFoundation()->getNodeFactory()->getNodeGroupFromPath($this->getPath());

        $contents = $this->compileView($this->compileString($this->files->get($path), $nodeGroup));

        if (!is_null($this->cachePath)) {
            $this->files->put($this->getCompiledPath($path), $contents);
        }
    }

    /**
     * Compile string
     *
     * @param string $content
     * @return string
     */
    public function compileString($content = '', $nodeGroup = NodeFactory::DEFAULT_NODE_GROUP)
    {
        if (!$this->getLexicon()->isPhpAllowed()) {
            $content = $this->escapePhp($content);
        }

        return $this->getRootNode($content, $nodeGroup)->compile();
    }

    /**
     * Get root node
     *
     * @param string $content
     * @return \Anomaly\Lexicon\Contract\Node\RootInterface
     */
    public function getRootNode($content = '', $nodeGroup = NodeFactory::DEFAULT_NODE_GROUP)
    {
        // TODO: Where to set and get node group
        return $this->getLexicon()->getFoundation()->getNodeFactory()->getRootNode($content, $nodeGroup);
    }

    /**
     * Compile the given string parse-able contents.
     *
     * @param $content
     * @internal param string $value
     * @return string
     */
    public function compileParse($content)
    {
        $compiledPath = $this->getCompiledPath($content);

        $this->setHash(substr(strrchr($compiledPath, '/'), 1));

        $contents = $this->compileView($this->compileString($content));

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
     * @param $content
     * @internal param string $path
     * @return bool
     */
    public function isNotParsed($content)
    {
        $isNotParsed = false;

        $compiled = $this->getCompiledPath($content);

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