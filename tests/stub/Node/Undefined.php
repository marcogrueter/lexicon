<?php namespace Anomaly\Lexicon\Stub\Node;

use Anomaly\Lexicon\Node\NodeType\Single;

/**
 * Class AnomalyLexiconUndefinedNode
 * This is a special node for testing the undefined variable exception
 *
 * @package Anomaly\Lexicon\Test
 */
class Undefined extends Single
{

    /**
     * Node name
     *
     * @var string
     */
    protected $name = 'undefined';

    /**
     * Compile source
     *
     * @return string
     */
    public function compile()
    {
        return 'echo $testingUndefinedVariableToCauseViewException;';
    }

} 