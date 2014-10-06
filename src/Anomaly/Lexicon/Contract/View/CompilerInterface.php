<?php namespace Anomaly\Lexicon\Contract\View;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Illuminate\View\Compilers\CompilerInterface as BaseCompilerInterface;

interface CompilerInterface extends BaseCompilerInterface
{

    /**
     * @param $lexicon LexiconInterface
     * @return mixed
     */
    public function setLexicon(LexiconInterface $lexicon);

    /**
     * @return LexiconInterface
     */
    public function getLexicon();

}