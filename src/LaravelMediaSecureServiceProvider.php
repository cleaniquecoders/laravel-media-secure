<?php

namespace Bekwoh\LaravelMediaSecure;

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
}
