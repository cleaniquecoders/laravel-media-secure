<?php

namespace Bekwoh\LaravelMediaSecure;

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

    public function packageBooted()
    {
        $model = config('laravel-media-secure.model');
        $policy = config('laravel-media-secure.policy');
        if (! is_null(Gate::getPolicyFor($model))) {
            Gate::policy($model, $policy);
        }
    }
}
