<?php namespace Anomaly\Lexicon\View;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\Node\NodeFactory;

/**
 * Class LexiconCompiler
 *
 * @package Anomaly\Lexicon\View
 */
class LexiconCompiler 
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
    public function compile($string)
    {
        return $this->getRootNode($string)->compile();
    }

    /**
     * Get root node
     *
     * @param string $content
     * @return \Anomaly\Lexicon\Contract\Node\RootInterface
     */
    public function getRootNode($string = '')
    {
        $lexicon = $this->getLexicon();

        /** @var NodeFactory $nodeFactory */
        $nodeFactory = $lexicon->getFoundation()->getNodeFactory();

        $nodeGroup = $nodeFactory->getNodeGroupFromPath($string);

        return $nodeFactory->getRootNode($string, $nodeGroup);
    }

} 