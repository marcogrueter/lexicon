<?php namespace Anomaly\Lexicon\Exception;

class RootNodeTypeNotFoundException extends \Exception
{

    protected $message = 'The root node type has not been registered.';

}