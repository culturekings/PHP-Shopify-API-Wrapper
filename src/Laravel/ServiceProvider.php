<?php

namespace Shopify\Laravel;

use Shopify;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

/**
 * Class ServiceProvider
 * @package Shopify\Laravel
 */
class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * @throws Shopify\Exception\FunctionNotFoundException
     */
    public function boot()
    {
        $configPath = __DIR__ . '/../config/config.php';

        // Attempt to work out the configuration path to publish
        if (function_exists('config_path')) {
            $publishPath = config_path('shopify.php');
        } elseif (function_exists('base_path')) {
            $publishPath = base_path('config/shopify.php');
        } else {
            throw new Shopify\Exception\FunctionNotFoundException('config_path and/or base_path');
        }

        // Publish our configuration
        $this->publishes(
            [
                $configPath => $publishPath,
            ],
            'config'
        );
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__ . '/../config/config.php';

        $this->mergeConfigFrom($configPath, 'shopify');

        $this->app->singleton('shopify', function($app) {
            // If no configuration available, return empty client
            if (!isset($app['config']['services']['shopify'])) {
                return new Shopify\Client();
            }

            // Otherwise, return a configured Shopify client
            $config = array_filter($app['config']['services']['shopify']);

            return new Shopify\Client($config);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'shopify',
        ];
    }
}
