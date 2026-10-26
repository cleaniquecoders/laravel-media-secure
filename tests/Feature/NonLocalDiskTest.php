<?php

use CleaniqueCoders\LaravelMediaSecure\Enums\MediaAccess;
use CleaniqueCoders\LaravelMediaSecure\Http\Middleware\ValidateMediaAccess;
use CleaniqueCoders\LaravelMediaSecure\LaravelMediaSecure;
use CleaniqueCoders\LaravelMediaSecure\Tests\Models\RemoteMedia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    if (! Schema::hasTable('media')) {
        Artisan::call('migrate', [
            '--path' => '../../../../tests/database/migrations/',
        ]);
    }

    Route::get('/login', fn () => 'login')->name('login');

    config()->set('laravel-media-secure.strict', false);
    config()->set('laravel-media-secure.middleware', [
        'auth',
        ValidateMediaAccess::class,
    ]);
    config()->set('laravel-media-secure.signed.enabled', true);

    // A non-default disk, faked, with a media model whose getPath() throws
    config()->set('filesystems.disks.remote_media', ['driver' => 'local', 'root' => storage_path('app/remote')]);
    Storage::fake('remote_media');
    config()->set('media-library.media_model', RemoteMedia::class);
    config()->set('laravel-media-secure.model', RemoteMedia::class);
});

function remoteMedia(): RemoteMedia
{
    $tempFile = sys_get_temp_dir().'/test_media_'.uniqid().'.txt';
    file_put_contents($tempFile, 'Remote content');

    return user()->addMedia($tempFile)->toMediaCollection('default', 'remote_media');
}

it('serves media from a non-default disk', function (string $type) {
    $media = remoteMedia();

    expect($media->disk)->toBe('remote_media');
    Storage::disk('remote_media')->assertExists($media->getPathRelativeToRoot());

    $response = login()->get(route('media', ['type' => $type, 'uuid' => $media->uuid]));

    $response->assertStatus(200)
        ->assertHeader('ETag')
        ->assertHeader('Last-Modified')
        ->assertHeader('Cache-Control');

    expect($response->streamedContent())->toBe('Remote content');

    $disposition = $type === MediaAccess::DOWNLOAD->value ? 'attachment' : 'inline';
    expect($response->headers->get('Content-Disposition'))->toStartWith($disposition);
})->with([MediaAccess::VIEW->value, MediaAccess::STREAM->value, MediaAccess::DOWNLOAD->value]);

it('returns 404 when the file is missing from a non-default disk', function () {
    $media = remoteMedia();
    Storage::disk('remote_media')->delete($media->getPathRelativeToRoot());

    // The policy denies missing files, so the authenticated route answers 403
    login()->get(route('media', ['type' => MediaAccess::VIEW->value, 'uuid' => $media->uuid]))
        ->assertStatus(403);

    $this->get(app(LaravelMediaSecure::class)->signedViewUrl($media))
        ->assertStatus(404);
});

it('returns 304 when the ETag matches on a non-default disk', function () {
    $media = remoteMedia();
    $url = route('media', ['type' => MediaAccess::VIEW->value, 'uuid' => $media->uuid]);

    $etag = login()->get($url)->assertStatus(200)->headers->get('ETag');

    login()->get($url, ['If-None-Match' => $etag])->assertStatus(304);
});
