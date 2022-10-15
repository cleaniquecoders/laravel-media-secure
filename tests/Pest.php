<?php

use Bekwoh\LaravelMediaSecure\Tests\Models\User;
use Bekwoh\LaravelMediaSecure\Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

uses(TestCase::class)->in(__DIR__);

function login($user = null)
{
    return test()->actingAs($user ?? user());
}

function user($name = 'pest', $email = 'pest@media.com', $password = 'password')
{
    return User::updateOrCreate([
        'name' => $name,
        'email' => $email,
    ], [
        'uuid' => Str::uuid()->toString(),
        'password' => Hash::make($password),
    ]);
}

function media($user)
{
    $file = __DIR__.'/file.test';
    if (file_exists($file)) {
        unlink($file);
    }

    touch($file);
    $user
        ->addMedia($file)
        ->toMediaCollection();

    return $user->getFirstMedia();
}
