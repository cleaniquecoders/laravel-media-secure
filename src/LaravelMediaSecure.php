<?php

namespace CleaniqueCoders\LaravelMediaSecure;

use Illuminate\Support\Facades\Route;

class LaravelMediaSecure
{
    public static function routes()
    {
        Route::get(
            config('laravel-media-secure.prefix').'/{type}/{uuid}',
            config('laravel-media-secure.controller')
        )
            ->name(
                config('laravel-media-secure.route_name')
            )
            ->middleware(
                config('laravel-media-secure.middleware', ['auth', 'verified'])
            );
    }
}
