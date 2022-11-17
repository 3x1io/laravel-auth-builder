<?php

namespace io3x1\LaravelAuthBuilder;

use Illuminate\Support\ServiceProvider;
use io3x1\LaravelAuthBuilder\Console\Commands\AuthBuilderGenerator;

class LaravelAuthBuilderProvider extends ServiceProvider
{
    /**
     * @var string $packageName
     */
    protected string $packageName = 'LaravelAuthBuilder';

    /**
     * @var string $packageNameLower
     */
    protected string $packageNameLower = 'laravel-auth-builder';

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerConfig();
        $this->commands([
            AuthBuilderGenerator::class
        ]);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig(): void
    {
        $this->publishes([
            __DIR__ . '/../config/laravel-auth-builder.php' => config_path($this->packageNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laravel-auth-builder.php', 'laravel-auth-builder'
        );
    }
}
