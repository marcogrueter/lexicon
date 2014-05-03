<?php namespace Aiws\Lexicon\Base;

class CallbackHandler implements CallbackHandlerInterface
{
    public function compile($name, $parameters, $content)
    {
        $parameters = var_export($parameters, true);

        return '\\'.get_called_class()."::call('{$name}', {$parameters}, '{$content}')";
    }

    public static function call($name, $parameters, $content)
    {
        /*        $parameters['hello'] = isset($parameters['hello']) ? $parameters['hello'] : 'World';

        return "Hello, <b>{$parameters['hello']}</b>. This is plugin content for the <b>{$name}</b> tag.";*/

        return false;
    }
}