<?php namespace Anomaly\Lexicon\View\Compiler;


use Anomaly\Lexicon\Contract\EnvironmentInterface;

class ViewCompiler
{
    /**
     * @var Compiler
     */
    protected $compiler;

    /**
     * @var EnvironmentInterface
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
        $newline = $this->getCompiler()->getLexicon()->isDebug() ? PHP_EOL : null;

        return $this->template(
            [
                '[class]'  => $this->getCompiler()->getHash(),
                '[source]' => $newline . trim($source),
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