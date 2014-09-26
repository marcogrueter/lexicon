<?php namespace Anomaly\Lexicon\Support;

use Anomaly\Lexicon\Contract\Support\ContainerInterface;
use Illuminate\Container\Container as BaseContainer;

/**
 * Class Container
 *
 * @package Anomaly\Lexicon\Support
 */
class Container extends BaseContainer implements ContainerInterface
{
    /**
     * Is the application booted
     *
     * @var bool
     */
    protected $booted = true;

    /**
     * @var array
     */
    protected $bootedCallbacks = [];

    /**
     * Get or check the current application environment.
     *
     * @param  mixed
     * @return string
     */
    public function environment()
    {
        return 'develop';
    }

    /**
     * Determine if the application is currently down for maintenance.
     *
     * @return bool
     */
    public function isDownForMaintenance()
    {
        return false;
    }

    /**
     * Register a service provider with the application.
     *
     * @param  \Illuminate\Support\ServiceProvider|string $provider
     * @param  array                                      $options
     * @param  bool                                       $force
     * @return \Illuminate\Support\ServiceProvider
     */
    public function register($provider, $options = array(), $force = false)
    {
        // TODO: Implement register() method.
    }

    /**
     * Register a deferred provider and service.
     *
     * @param  string $provider
     * @param  string $service
     * @return void
     */
    public function registerDeferredProvider($provider, $service = null)
    {
        // TODO: Implement registerDeferredProvider() method.
    }

    /**
     * Determine if the application has booted.
     *
     * @return bool
     */
    public function isBooted()
    {
        return $this->booted;
    }

    /**
     * Boot the application's service providers.
     *
     * @return \Illuminate\Container\Container|void
     */
    public function boot()
    {
        $this->booted = true;
        return $this;
    }

    /**
     * Register a new boot listener.
     *
     * @param  mixed $callback
     * @return void
     */
    public function booting($callback)
    {
        // TODO: Implement booting() method.
    }

    /**
     * Register a new "booted" listener.
     *
     * @param  mixed  $callback
     * @return void
     */
    public function booted($callback)
    {
        $this->bootedCallbacks[] = $callback;

        if ($this->isBooted()) $this->fireAppCallbacks(array($callback));
    }

    /**
     * Call the booting callbacks for the application.
     *
     * @param  array  $callbacks
     * @return void
     */
    public function fireAppCallbacks(array $callbacks)
    {
        foreach ($callbacks as $callback)
        {
            call_user_func($callback, $this);
        }
    }
}