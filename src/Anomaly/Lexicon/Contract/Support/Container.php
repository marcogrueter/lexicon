<?php namespace Anomaly\Lexicon\Contract\Support;

use Illuminate\Contracts\Container\Container as BaseContainer;

/**
 * Class Container
 *
 * @package src\Anomaly\Lexicon\Contract\Support
 */
interface Container extends BaseContainer
{

    /**
     * Bind a shared Closure into the container.
     *
     * @param  string    $abstract
     * @param  \Closure  $closure
     * @return void
     */
    public function bindShared($abstract, \Closure $closure);

    /**
     * Wrap a Closure such that it is shared.
     *
     * @param  \Closure  $closure
     * @return \Closure
     */
    public function share(\Closure $closure);

    /**
     * Register a new "booted" listener.
     *
     * @param  mixed  $callback
     * @return void
     */
    public function booted(\Closure $callback);


    /**
     * @param $abstract
     * @param $concrete
     * @return mixed
     */
    public function instance($abstract, $concrete);
} 