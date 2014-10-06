<?php namespace Anomaly\Lexicon\Contract\View;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Illuminate\View\Compilers\CompilerInterface;

interface CompilerSequenceInterface extends CompilerInterface
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