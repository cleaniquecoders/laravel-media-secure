<?php

use Illuminate\Support\Facades\Route;

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
