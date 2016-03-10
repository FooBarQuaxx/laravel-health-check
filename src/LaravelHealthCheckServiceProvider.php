<?php

namespace NpmWeb\LaravelHealthCheck;

use Illuminate\Support\ServiceProvider;
use Log;

class LaravelHealthCheckServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    protected $configFilePath;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->configFilePath = __DIR__.'/../config/config.php';
    }

    /**
     * Bootstrap the application events.
     *
     * Binds an array of HealthCheckInterface to 'health-checks' in IoC
     *
     * There can be multiple instances of a type of check. If so,
     * the config for that check will be an array of arrays; if it's a single,
     * instance name defaults to "default" and config is array of vals
     *
     * @return void
     */
    public function boot() //Router $router)
    {
        $this->publishes([ $this->configFilePath => config_path('laravel-health-check.php')]);
        $this->loadViewsFrom(__DIR__.'../views', 'laravel-health-check');
        $this->mergeConfigFrom( $this->configFilePath, 'laravel-health-check' );

        $this->bootRoutes($this->app['router'], 'monitor/health'); // TODO: make prefix configurable

        $this->app->singleton('health-checks', function($app) {
            return new HealthCheckManager($app);
        });
    }

    protected function bootRoutes($router, $prefix)
    {
        $router->group(['prefix' => $prefix], function($router) {
            $router->get('',
                ['uses' => 'NpmWeb\LaravelHealthCheck\Controllers\HealthCheckController@index', 'as' => 'health_check.index']);

            $router->get('{check}',
                ['uses' => 'NpmWeb\LaravelHealthCheck\Controllers\HealthCheckController@show', , 'as' => 'health_check.show']);
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

}
