<?php

use CleaniqueCoders\LaravelMediaSecure\Enums\MediaAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

use function Pest\Laravel\get;
use function PHPUnit\Framework\assertTrue;

uses(RefreshDatabase::class);

beforeEach(function () {
    if (! Schema::hasTable('media')) {
        Artisan::call('migrate', [
            '--path' => '../../../../tests/database/migrations/',
        ]);
    }

    Route::get('/login', fn () => 'login')->name('login');

    // Disable strict mode for testing (no parent model policy required)
    config()->set('laravel-media-secure.strict', false);

    // Use simpler middleware for testing (no email verification required)
    config()->set('laravel-media-secure.middleware', [
        'auth',
        \CleaniqueCoders\LaravelMediaSecure\Http\Middleware\ValidateMediaAccess::class,
    ]);
});

it('cannot view media if not logged in', function () {
    get(route('media', [
        'type' => MediaAccess::VIEW->value,
        'uuid' => '123asd',
    ]))->assertStatus(302);
})->group('view');

it('cannot download media if not logged in', function () {
    get(route('media', [
        'type' => MediaAccess::DOWNLOAD->value,
        'uuid' => '123asd',
    ]))->assertStatus(302);
})->group('download');

it('cannot stream media if not logged in', function () {
    get(route('media', [
        'type' => MediaAccess::STREAM->value,
        'uuid' => '123asd',
    ]))->assertStatus(302);
})->group('stream');

it('cannot view media if media do not exist', function () {
    login()
        ->get(route('media', [
            'type' => MediaAccess::VIEW->value,
            'uuid' => '123asd',
        ]))->assertStatus(404);
})->group('view');

it('cannot download media if media do not exist', function () {
    login()
        ->get(route('media', [
            'type' => MediaAccess::DOWNLOAD->value,
            'uuid' => '123asd',
        ]))->assertStatus(404);
})->group('download');

it('cannot stream media if media do not exist', function () {
    login()
        ->get(route('media', [
            'type' => MediaAccess::STREAM->value,
            'uuid' => '123asd',
        ]))->assertStatus(404);
})->group('stream');

it('can view media if media do exist', function () {
    $user = user();
    $media = media($user);

    expect($media)->not->toBeNull();
    expect($media->uuid)->not->toBeEmpty();
    expect(file_exists($media->getPath()))->toBeTrue();

    $response = login($user)
        ->get(route('media', [
            'type' => MediaAccess::VIEW->value,
            'uuid' => $media->uuid,
        ]));

    $response->assertStatus(200);

    // Content-Type may include charset suffix (e.g., "text/plain; charset=UTF-8")
    $contentType = $response->headers->get('Content-Type');
    expect($contentType)->toStartWith($media->mime_type);
})->group('view');

it('can download media if media do exist', function () {
    $user = user();
    $media = media($user);

    expect($media)->not->toBeNull();
    expect($media->uuid)->not->toBeEmpty();
    expect(file_exists($media->getPath()))->toBeTrue();

    login($user)
        ->get(route('media', [
            'type' => MediaAccess::DOWNLOAD->value,
            'uuid' => $media->uuid,
        ]))
        ->assertStatus(200)
        ->assertHeader('Content-Disposition');
})->group('download');

it('can stream media if media do exist', function () {
    $user = user();
    $media = media($user);

    expect($media)->not->toBeNull();
    expect($media->uuid)->not->toBeEmpty();
    expect(file_exists($media->getPath()))->toBeTrue();

    $response = login($user)
        ->get(route('media', [
            'type' => MediaAccess::STREAM->value,
            'uuid' => $media->uuid,
        ]));

    $response->assertStatus(200);

    // Content-Type may include charset suffix (e.g., "text/plain; charset=UTF-8")
    $contentType = $response->headers->get('Content-Type');
    expect($contentType)->toStartWith($media->mime_type);
})->group('stream');

it('has helpers', function () {
    assertTrue(function_exists('get_media_url'));
    assertTrue(function_exists('get_view_media_url'));
    assertTrue(function_exists('get_download_media_url'));
    assertTrue(function_exists('get_stream_media_url'));
});

it('returns 422 for invalid access type', function () {
    login()
        ->get(route('media', [
            'type' => 'invalid-type',
            'uuid' => '123asd',
        ]))->assertStatus(422);
})->group('validation');

it('returns correct ETag header for caching', function () {
    $user = user();
    $media = media($user);

    $response = login($user)
        ->get(route('media', [
            'type' => MediaAccess::VIEW->value,
            'uuid' => $media->uuid,
        ]));

    $response->assertStatus(200)
        ->assertHeader('ETag')
        ->assertHeader('Last-Modified')
        ->assertHeader('Cache-Control');
})->group('caching');
