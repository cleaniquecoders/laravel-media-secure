<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authenticated Media Routes
|--------------------------------------------------------------------------
|
| These routes require authentication and use the full middleware stack
| defined in the configuration. Users must be logged in to access media.
|
*/

Route::get(
    config('laravel-media-secure.prefix').'/{type}/{uuid}',
    config('laravel-media-secure.controller')
)
    ->name(
        config('laravel-media-secure.route_name')
    )
    ->middleware(
        config('laravel-media-secure.middleware', ['auth', 'verified', 'validate-media-access'])
    );

/*
|--------------------------------------------------------------------------
| Signed Media Routes
|--------------------------------------------------------------------------
|
| These routes allow unauthenticated access to media using signed URLs.
| The URL must contain a valid signature and not be expired.
|
*/

if (config('laravel-media-secure.signed.enabled', true)) {
    Route::get(
        config('laravel-media-secure.signed.prefix', 'media-signed').'/{type}/{uuid}',
        config('laravel-media-secure.controller')
    )
        ->name(
            config('laravel-media-secure.signed.route_name', 'media.signed')
        )
        ->middleware(
            config('laravel-media-secure.signed.middleware', ['validate-signed-media-access'])
        );
}
