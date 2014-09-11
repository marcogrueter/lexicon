<?php namespace Anomaly\Lexicon\View;

use Anomaly\Lexicon\Contract\View\CompilerInterface;
use Anomaly\Lexicon\Contract\LexiconInterface;
use Illuminate\View\Compilers\Compiler as BaseCompiler;

class Compiler extends BaseCompiler implements CompilerInterface
{
    /**
     * @var LexiconInterface
     */
    protected $lexicon;

    protected $hash;

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
        $compiledPath = $this->getCompiledPath($path);

        $segments = explode('/', $compiledPath);

        $this->setHash($segments[count($segments) - 1]);

        $contents = $this->compileString($this->files->get($path));

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
    public function compileString($content = '')
    {
        if (!$this->getLexicon()->allowPhp()) {
            $content = $this->escapePhp($content);
        }

        return $this->compileView($this->compileRootNode($content));
    }

    /**
     * Compile root node
     *
     * @param string $content
     * @return mixed|string
     */
    public function compileRootNode($content = '')
    {
        $rootNode = $this->getRootNode($content);

        $source = $rootNode->compile();

        $footer = $rootNode->getFooter();

        if (count($footer) > 0) {
            $source = str_replace('@parent', '', $source);
            $source = ltrim($source, PHP_EOL) . PHP_EOL . implode(PHP_EOL, array_reverse($footer));
        }

        return $source;
    }

    /**
     * Get root node
     *
     * @param string $content
     * @return \Anomaly\Lexicon\Contract\Node\RootInterface
     */
    public function getRootNode($content = '')
    {
        $rootNode = $this->getLexicon()->getRootNodeType()->make(
            array(
                'name'    => 'root',
                'content' => $content,
            )
        );

        return $rootNode->createChildNodes();
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
     * @param $content
     * @internal param string $path
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

    /**
     * Compile the view from the template
     *
     * @param string $source
     * @return string Compiled template
     */
    protected function compileView($source = '')
    {
        $data = [
            '[namespace]' => $this->getLexicon()->getViewNamespace(),
            '[class]'     => $this->getLexicon()->getViewClass($this->getHash()),
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
        return file_get_contents($this->getLexicon()->getViewTemplatePath());
    }

}