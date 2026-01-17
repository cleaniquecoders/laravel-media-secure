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

        // Ensure storage directories exist
        $this->ensureStorageDirectoriesExist();
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

        // Configure the media storage disk
        config()->set('filesystems.disks.private_media', [
            'driver' => 'local',
            'root' => storage_path('app/private/media'),
            'visibility' => 'private',
        ]);

        // Configure Spatie MediaLibrary
        config()->set('media-library.disk_name', 'private_media');
        config()->set('media-library.max_file_size', 1024 * 1024 * 10);
        config()->set('media-library.queue_conversions_by_default', false);
        config()->set('media-library.media_model', \Spatie\MediaLibrary\MediaCollections\Models\Media::class);

        // Set a custom temporary directory for media uploads
        $tempDir = storage_path('app/temp');
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        config()->set('media-library.temporary_directory_path', $tempDir);
    }

    protected function ensureStorageDirectoriesExist(): void
    {
        $directories = [
            storage_path('app/private/media'),
            storage_path('app/temp'),
        ];

        foreach ($directories as $dir) {
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }
}
