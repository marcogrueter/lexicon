<?php namespace Anomaly\Lexicon\Test\Node;

use Anomaly\Lexicon\Contract\Node\ValidatorInterface;

/**
 * Class TestValidator

 *
*@package Anomaly\Lexicon\Test\Node
 */
class TestValidator implements ValidatorInterface
{
    public function isValid()
    {
        return true;
    }
} 