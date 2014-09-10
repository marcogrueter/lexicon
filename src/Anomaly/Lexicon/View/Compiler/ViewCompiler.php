<?php namespace Anomaly\Lexicon\View\Compiler;


use Anomaly\Lexicon\Contract\LexiconInterface;

class ViewCompiler
{
    /**
     * @var Compiler
     */
    protected $compiler;

    /**
     * @var LexiconInterface
     */
    protected $lexicon;

    public function __construct(Compiler $compiler)
    {
        $this->compiler = $compiler;
    }

    public function getCompiler()
    {
        return $this->compiler;
    }

    public function compile()
    {
        return $this->view($this->getCompiler()->getStreamCompiler()->compile());
    }

    public function view($source)
    {
        $lexicon = $this->getCompiler()->getLexicon();

        return $this->template(
            [
                '[namespace]' => $lexicon->getViewNamespace(),
                '[class]'     => $lexicon->getViewClass($this->getCompiler()->getHash()),
                '[source]'    => $source,
            ]
        );
    }

    /**
     * Return the compiled template for a model.
     *
     * @param array $data
     * @return string Compiled template
     */
    protected function template($data = [])
    {
        return str_replace(array_keys($data), $data, $this->getCompiler()->getLexicon()->getViewTemplate());
    }
}