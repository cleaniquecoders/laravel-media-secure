<?php

use Bekwoh\LaravelMediaSecure\Enums\MediaAccess;
use Bekwoh\LaravelMediaSecure\LaravelMediaSecure;
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

    LaravelMediaSecure::routes();
    Route::get('/login', fn () => 'login')->name('login');
});

it('cannot view media if not logged in', function () {
    get(route('media', [
        'type' => MediaAccess::view()->value,
        'uuid' => '123asd',
    ]))->assertStatus(302);
})->group('view');

it('cannot download media if not logged in', function () {
    get(route('media', [
        'type' => MediaAccess::download()->value,
        'uuid' => '123asd',
    ]))->assertStatus(302);
})->group('download');

it('cannot view media if media do not exist', function () {
    login()
        ->get(route('media', [
            'type' => MediaAccess::view()->value,
            'uuid' => '123asd',
        ]))->assertStatus(404);
})->group('view');

it('cannot download media if media do not exist', function () {
    login()
        ->get(route('media', [
            'type' => MediaAccess::view()->value,
            'uuid' => '123asd',
        ]))->assertStatus(404);
})->group('download');

it('can view media if media do not exist', function () {
    $user = user();
    $media = media($user);
    login($user)
        ->get(route('media', [
            'type' => MediaAccess::view()->value,
            'uuid' => $media->uuid,
        ]))->assertStatus(200);
})->group('view')->skip('The test unable to add media at the moment.');

it('can download media if media do not exist', function () {
    $user = user();
    $media = media($user);
    login($user)
        ->get(route('media', [
            'type' => MediaAccess::view()->value,
            'uuid' => $media->uuid,
        ]))->assertStatus(200);
})->group('download')->skip('The test unable to add media at the moment.');

it('has helpers', function () {
    assertTrue(function_exists('get_media_url'));
    assertTrue(function_exists('get_view_media_url'));
    assertTrue(function_exists('get_download_media_url'));
});
