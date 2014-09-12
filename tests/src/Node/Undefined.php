<?php namespace Anomaly\Lexicon\Test\Node;

use Anomaly\Lexicon\Node\Single;

/**
 * Class AnomalyLexiconUndefinedNode
 * This is a special node for testing the undefined variable exception
 *
 * @package Anomaly\Lexicon\Test
 */
class Undefined extends Single
{
    protected $name = 'undefined';

    public function compile()
    {
        return 'echo $testingUndefinedVariableToCauseViewException;';
    }
}