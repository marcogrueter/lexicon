<?php namespace Anomaly\Lexicon\Contract;

use Illuminate\View\Compilers\CompilerInterface as BaseCompilerInterface;

interface CompilerInterface extends BaseCompilerInterface
{

    /**
     * Compile
     *
     * @param $string
     * @return mixed
     */
    public function compile($string);

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
    public function setLexicon($lexicon);

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