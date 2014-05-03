<?php namespace Aiws\Lexicon\Base;

interface CallbackHandlerInterface
{
    public function compile($name, $parameters, $content);

    public static function call($name, $parameters, $content);
}