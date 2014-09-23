<?php namespace Anomaly\Lexicon\Stub;

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

        $this->printNodeTypes($this->lexicon->getNodeTypes());

        $this->header('Attribute Node Types Info');
        $this->header('Total: '. count($this->lexicon->getAttributeNodeTypes()));

        $this->printNodeTypes($this->lexicon->getAttributeNodeTypes());
    }

    public function printNodeTypes(array $nodeTypes)
    {
        foreach ($nodeTypes as $i => $nodeType) {
            print ($i + 1) . ". " . get_class($nodeType) . "\n" .
                "    regex: " . $nodeType->regex() . "\n";
            print "\n";
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