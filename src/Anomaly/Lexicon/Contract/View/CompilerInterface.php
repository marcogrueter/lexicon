<?php namespace Anomaly\Lexicon\Contract\View;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Illuminate\View\Compilers\CompilerInterface as BaseCompilerInterface;

interface CompilerInterface extends BaseCompilerInterface
{

    /**
     * Compile parse-able content
     *
     * @param $string
     * @return string
     */
    public function compileParse($string);

    /**
     * Is the view not parsed yet
     *
     * @param $path
     * @return bool
     */
    public function isNotParsed($path);

    /**
     * @param $lexicon LexiconInterface
     * @return mixed
     */
    public function setLexicon(LexiconInterface $lexicon);

    /**
     * @return LexiconInterface
     */
    public function getLexicon();

    /**
     * Get view template
     *
     * @return string
     */
    public function getViewTemplate();

}