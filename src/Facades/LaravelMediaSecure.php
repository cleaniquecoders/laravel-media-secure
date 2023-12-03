<?php

namespace CleaniqueCoders\LaravelMediaSecure\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \CleaniqueCoders\LaravelMediaSecure\LaravelMediaSecure
 */
class LaravelMediaSecure extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \CleaniqueCoders\LaravelMediaSecure\LaravelMediaSecure::class;
    }
}
