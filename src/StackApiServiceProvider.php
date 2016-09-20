<?php
namespace Nahid\StackApis;
use Illuminate\Container\Container;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

class StackApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupConfig();
    }
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerStackApi();
        
    }
    /**
     * Setup the config.
     */
    protected function setupConfig()
    {
        $source = realpath(__DIR__ . '/../config/stackapi.php');
        // Check if the application is a Laravel OR Lumen instance to properly merge the configuration file.
        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('stackapi.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('stackapi');
        }
        $this->mergeConfigFrom($source, 'stackapi');
    }
  
    /**
     * Register Talk class.
     *
     * @return void
     */
    protected function registerStackApi()
    {
        $this->app->singleton('StackApi', function (Container $app) {
            return new StackApi($app['config']->get('stackapi'));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return [
            StackApi::class
        ];
    }
}