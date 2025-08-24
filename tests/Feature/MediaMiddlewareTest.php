<?php

use CleaniqueCoders\LaravelMediaSecure\Http\Middleware\ValidateMediaAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpKernel\Exception\HttpException;

uses(RefreshDatabase::class);

// Set up a login route for redirects and migrations
beforeEach(function () {
    // Set up migrations if they don't exist
    if (! Schema::hasTable('users')) {
        Artisan::call('migrate', [
            '--path' => '../../../../tests/database/migrations/',
        ]);
    }

    Route::get('/login', fn () => 'login')->name('login');
});

it('validates access type in the middleware', function () {
    // Set up test route with middleware
    Route::get('/test-media/{type}/{uuid}', function (Request $request, $type, $uuid) {
        return 'passed';
    })->middleware(ValidateMediaAccess::class);

    // Remove exception handling to see the actual exceptions
    $this->withoutExceptionHandling();

    // Create a real user for testing
    $user = user();
    login($user);

    // Test with invalid type - should throw an exception
    $this->expectException(HttpException::class);
    $this->expectExceptionMessage('Invalid request type of invalid-type');

    $this->get('/test-media/invalid-type/123-uuid');
})->group('middleware');

it('checks authorization in the middleware', function () {
    // Set up test route with middleware
    Route::get('/test-media/{type}/{uuid}', function (Request $request, $type, $uuid) {
        return 'passed';
    })->middleware(ValidateMediaAccess::class);

    // Remove exception handling
    $this->withoutExceptionHandling();

    // Create a real user for testing
    $user = user();
    login($user);

    // Create a fake media UUID that doesn't exist
    $fakeUuid = 'fake-uuid-123';

    // Should throw model not found exception because media doesn't exist
    $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

    $this->get('/test-media/view/'.$fakeUuid);
})->group('middleware');

it('adds media to the request attributes', function () {
    // Create test route that checks for media in request attributes
    Route::get('/test-media-attributes/{type}/{uuid}', function (Request $request, $type, $uuid) {
        return $request->attributes->has('media') ? 'has media' : 'no media';
    })->middleware(ValidateMediaAccess::class);

    // Create a real user for testing
    $user = user();
    login($user);

    // Create a real media record in the database
    $media = new \Spatie\MediaLibrary\MediaCollections\Models\Media([
        'model_type' => get_class($user),
        'model_id' => $user->id,
        'uuid' => 'test-uuid-123',
        'collection_name' => 'default',
        'name' => 'test-file',
        'file_name' => 'test.txt',
        'mime_type' => 'text/plain',
        'disk' => 'public',
        'conversions_disk' => 'public',
        'size' => 100,
        'manipulations' => '[]',
        'custom_properties' => '{}',
        'generated_conversions' => '{}',
        'responsive_images' => '{}',
    ]);
    $media->save();

    // Mock the Gate to allow access
    Gate::shouldReceive('forUser')->andReturnSelf();
    Gate::shouldReceive('check')->andReturn(true);

    // Test that media was added to the request attributes
    $this->get('/test-media-attributes/view/'.$media->uuid)
        ->assertSuccessful()
        ->assertSee('has media');
})->group('middleware');

// Test for each media type
it('handles view media type correctly', function () {
    // Set up test route with middleware
    Route::get('/test-media/{type}/{uuid}', function (Request $request, $type, $uuid) {
        // Check that the type is correctly passed
        return 'Type: '.$type;
    })->middleware(ValidateMediaAccess::class);

    // Create a real user for testing
    $user = user();
    login($user);

    // Create a real media record in the database
    $media = new \Spatie\MediaLibrary\MediaCollections\Models\Media([
        'model_type' => get_class($user),
        'model_id' => $user->id,
        'uuid' => 'test-uuid-view',
        'collection_name' => 'default',
        'name' => 'test-file',
        'file_name' => 'test.txt',
        'mime_type' => 'text/plain',
        'disk' => 'public',
        'conversions_disk' => 'public',
        'size' => 100,
        'manipulations' => '[]',
        'custom_properties' => '{}',
        'generated_conversions' => '{}',
        'responsive_images' => '{}',
    ]);
    $media->save();

    // Mock the Gate to allow access
    Gate::shouldReceive('forUser')->andReturnSelf();
    Gate::shouldReceive('check')->andReturn(true);

    $this->get('/test-media/view/'.$media->uuid)
        ->assertSuccessful()
        ->assertSee('Type: view');
})->group('middleware');

it('handles download media type correctly', function () {
    // Set up test route with middleware
    Route::get('/test-media/{type}/{uuid}', function (Request $request, $type, $uuid) {
        // Check that the type is correctly passed
        return 'Type: '.$type;
    })->middleware(ValidateMediaAccess::class);

    // Create a real user for testing
    $user = user();
    login($user);

    // Create a real media record in the database
    $media = new \Spatie\MediaLibrary\MediaCollections\Models\Media([
        'model_type' => get_class($user),
        'model_id' => $user->id,
        'uuid' => 'test-uuid-download',
        'collection_name' => 'default',
        'name' => 'test-file',
        'file_name' => 'test.txt',
        'mime_type' => 'text/plain',
        'disk' => 'public',
        'conversions_disk' => 'public',
        'size' => 100,
        'manipulations' => '[]',
        'custom_properties' => '{}',
        'generated_conversions' => '{}',
        'responsive_images' => '{}',
    ]);
    $media->save();

    // Mock the Gate to allow access
    Gate::shouldReceive('forUser')->andReturnSelf();
    Gate::shouldReceive('check')->andReturn(true);

    $this->get('/test-media/download/'.$media->uuid)
        ->assertSuccessful()
        ->assertSee('Type: download');
})->group('middleware');

it('handles stream media type correctly', function () {
    // Set up test route with middleware
    Route::get('/test-media/{type}/{uuid}', function (Request $request, $type, $uuid) {
        // Check that the type is correctly passed
        return 'Type: '.$type;
    })->middleware(ValidateMediaAccess::class);

    // Create a real user for testing
    $user = user();
    login($user);

    // Create a real media record in the database
    $media = new \Spatie\MediaLibrary\MediaCollections\Models\Media([
        'model_type' => get_class($user),
        'model_id' => $user->id,
        'uuid' => 'test-uuid-stream',
        'collection_name' => 'default',
        'name' => 'test-file',
        'file_name' => 'test.txt',
        'mime_type' => 'text/plain',
        'disk' => 'public',
        'conversions_disk' => 'public',
        'size' => 100,
        'manipulations' => '[]',
        'custom_properties' => '{}',
        'generated_conversions' => '{}',
        'responsive_images' => '{}',
    ]);
    $media->save();

    // Mock the Gate to allow access
    Gate::shouldReceive('forUser')->andReturnSelf();
    Gate::shouldReceive('check')->andReturn(true);

    $this->get('/test-media/stream/'.$media->uuid)
        ->assertSuccessful()
        ->assertSee('Type: stream');
})->group('middleware');
