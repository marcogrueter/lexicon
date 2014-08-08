<?php namespace Aiws\Lexicon\Provider\Laravel;

use Illuminate\Support\Facades\Facade;

/**
 * Class LexiconFacade
 *
 * @package Aiws\Lexicon\Provider\Laravel
 */
class LexiconFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'lexicon';
    }
}