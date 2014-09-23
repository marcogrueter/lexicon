<?php namespace Anomaly\Lexicon\Support;

use Illuminate\Container\Container as BaseContainer;

/**
 * Class Container
 *
 * @package Anomaly\Lexicon\Support
 */
class Container extends BaseContainer implements \Anomaly\Lexicon\Contract\Support\Container
{

    /**
     * Register a new "booted" listener.
     *
     * @param  mixed $callback
     * @return void
     */
    public function booted(\Closure $callback)
    {
        // TODO: Implement booted() method.
    }
}