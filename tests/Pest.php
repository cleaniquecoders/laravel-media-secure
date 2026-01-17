<?php

use CleaniqueCoders\LaravelMediaSecure\Tests\Models\User;
use CleaniqueCoders\LaravelMediaSecure\Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

uses(TestCase::class)->in(__DIR__);

function login(?User $user = null)
{
    return test()->actingAs($user ?? user());
}

function user($name = 'pest', $email = 'pest@media.com', $password = 'password'): User
{
    return User::updateOrCreate([
        'name' => $name,
        'email' => $email,
    ], [
        'uuid' => Str::uuid()->toString(),
        'password' => Hash::make($password),
    ]);
}

function media(User $user): Media
{
    // Create a temporary file for media upload
    $tempFile = sys_get_temp_dir().'/test_media_'.Str::random(8).'.txt';
    file_put_contents($tempFile, 'Test content for media file');

    // Add media using the temp file
    $media = $user
        ->addMedia($tempFile)
        ->usingName('Test File')
        ->toMediaCollection('default');

    return $media;
}
