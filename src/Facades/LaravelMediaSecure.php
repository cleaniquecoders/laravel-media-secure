<?php

namespace Bekwoh\LaravelMediaSecure\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Bekwoh\LaravelMediaSecure\LaravelMediaSecure
 */
class LaravelMediaSecure extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Bekwoh\LaravelMediaSecure\LaravelMediaSecure::class;
    }
}
