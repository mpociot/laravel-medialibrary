<?php

namespace Spatie\MediaLibrary;

use Illuminate\Support\ServiceProvider;
use Spatie\MediaLibrary\Commands\RegenerateCommand;

class MediaLibraryServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        Media::observe(new MediaObserver());

        $this->publishes([
            __DIR__.'/../resources/config/laravel-medialibrary.php' => $this->app->configPath().'/'.'laravel-medialibrary.php',
        ], 'config');

        if (!class_exists('CreateMediaTable')) {

            // Publish the migration
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__.'/../resources/migrations/create_media_table.php.stub' => $this->app->basePath().'/'.'database/migrations/'.$timestamp.'_create_media_table.php',
            ], 'migrations');
        }
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../resources/config/laravel-medialibrary.php', 'laravel-medialibrary');

        $this->app->singleton(MediaRepository::class);

        $this->app['command.medialibrary:regenerate'] = $this->app->make(RegenerateCommand::class);

        $this->commands(['command.medialibrary:regenerate']);
    }
}
