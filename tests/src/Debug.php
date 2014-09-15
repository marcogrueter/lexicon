<?php namespace Anomaly\Lexicon\Test;

use Anomaly\Lexicon\Contract\LexiconInterface;
use Illuminate\Contracts\Container\Container;

/**
 * Class Debug
 *
 * @package Anomaly\Lexicon\Test
 */
class Debug
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
     * Print node type regex list
     */
    public function printNodeTypesRegexList()
    {
        $this->header('Node Types Info');
        $this->header('Total: '. count($this->lexicon->getNodeTypes()));

        foreach ($this->lexicon->getNodeTypes() as $i => $nodeType) {
            print ($i + 1) . ". " . get_class($nodeType) . "\n" .
                "    regex: " . $nodeType->regex() . "\n";
        }
    }

    /**
     * @param $line
     */
    public function header($line)
    {
        print "{$line} \n\n";
    }

} 