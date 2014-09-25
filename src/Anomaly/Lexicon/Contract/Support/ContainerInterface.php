<?php namespace Anomaly\Lexicon\Contract\Support;

use Illuminate\Contracts\Foundation\Application;

/**
 * Class Container
 *
 * @package src\Anomaly\Lexicon\Contract\Support
 */
interface ContainerInterface extends Application, \ArrayAccess
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
     * @param $abstract
     * @param $concrete
     * @return mixed
     */
    public function instance($abstract, $concrete);
} 