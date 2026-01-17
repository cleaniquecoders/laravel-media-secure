<?php

namespace CleaniqueCoders\LaravelMediaSecure;

use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelMediaSecureServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-media-secure')
            ->hasConfigFile('laravel-media-secure')
            ->hasRoute('web');
    }

    public function registeringPackage(): void
    {
        $this->app->singleton(LaravelMediaSecure::class, function () {
            return new LaravelMediaSecure;
        });
    }

    public function bootingPackage(): void
    {
        Gate::policy(
            config('laravel-media-secure.model'),
            config('laravel-media-secure.policy'),
        );

        $this->registerMiddleware();
    }

    protected function registerMiddleware(): void
    {
        $router = $this->app['router'];

        // Register the middleware with its alias if using Laravel < 10.0
        if (method_exists($router, 'aliasMiddleware')) {
            $router->aliasMiddleware('validate-media-access', \CleaniqueCoders\LaravelMediaSecure\Http\Middleware\ValidateMediaAccess::class);
        }
    }
}
