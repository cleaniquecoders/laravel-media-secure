<?php

namespace CleaniqueCoders\LaravelMediaSecure\Tests\Models;

use Illuminate\Foundation\Auth\User as Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $guarded = ['id'];
}
