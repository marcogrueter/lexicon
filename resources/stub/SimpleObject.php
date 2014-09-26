<?php namespace Anomaly\Lexicon\Stub;

/**
 * Class SimpleObject
 *
 * @package Anomaly\Lexicon\Stub
 */
class SimpleObject 
{

    public $bar = 'value from property';

    public function foo()
    {
        return 'value from method';
    }

    public function fragile(array $attribute)
    {

    }

} 