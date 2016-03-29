<?php namespace EFrane\Transfugio;

use Illuminate\Support\ServiceProvider;

/**
 * Laravel Service Provider
 *
 * Integrates Transfugio with Laravel.
 *
 * @package EFrane\Transfugio
 **/
class TransfugioServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/transfugio.php', 'transfugio');
    }

    /**
     * Include the helper methods and set the view directory.
     *
     * @return void
     */
    public function boot()
    {
        require __DIR__ . '/helpers.php';

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'transfugio');

        $this->publishes([
            __DIR__ . '/../config/transfugio.php' => config_path('transfugio.php'),
            __DIR__ . '/../resources/views'       => resource_path('views/vendor/transfugio'),
        ]);
    }
}
