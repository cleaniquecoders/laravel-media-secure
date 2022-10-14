<?php

namespace Bekwoh\LaravelMediaSecure;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Bekwoh\LaravelMediaSecure\Commands\LaravelMediaSecureCommand;

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
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-media-secure_table')
            ->hasCommand(LaravelMediaSecureCommand::class);
    }
}
