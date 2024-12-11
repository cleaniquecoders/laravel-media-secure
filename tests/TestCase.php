<?php

namespace CleaniqueCoders\LaravelMediaSecure\Tests;

use CleaniqueCoders\LaravelMediaSecure\LaravelMediaSecureServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'CleaniqueCoders\\LaravelMediaSecure\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelMediaSecureServiceProvider::class,
            MediaLibraryServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        config()->set('filesystems.disks.private_media', [
            'driver' => 'local',
            'root' => storage_path('app/private/media'),
            'visibility' => 'private',
        ]);

        config()->set('media-library', [
            'disk_name' => 'private_media',
            'max_file_size' => 1024 * 1024 * 10, // 10MB
            'queue_conversions_by_default' => false,
            'media_model' => \Spatie\MediaLibrary\MediaCollections\Models\Media::class,
        ]);

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-media-secure_table.php.stub';
        $migration->up();
        */
    }
}
