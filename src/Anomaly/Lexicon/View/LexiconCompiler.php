<?php namespace Anomaly\Lexicon\View;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Contract\View\CompilerInterface;
use Anomaly\Lexicon\Stub\LexiconStub;

/**
 * Class LexiconCompiler
 *
 * @package Anomaly\Lexicon\View
 */
class LexiconCompiler implements CompilerInterface
{

    /**
     * @var LexiconInterface
     */
    private $lexicon;

    /**
     * @param LexiconInterface $lexicon
     */
    public function __construct(LexiconInterface $lexicon)
    {
        $this->lexicon = $lexicon;
    }

    /**
     * @return mixed
     */
    public function getLexicon()
    {
        return $this->lexicon;
    }

    /**
     * Compile string
     *
     * @param $string
     * @return string
     */
    public function compile($string, $path = null)
    {
        return $this
            ->getLexicon()
            ->getFoundation()
            ->getNodeFactory()
            ->getRootNode($string, $path)
            ->compile();
    }

    public static function stub()
    {
        return new static(LexiconStub::get());
    }

} 