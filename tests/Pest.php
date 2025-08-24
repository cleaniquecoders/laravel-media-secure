<?php

use CleaniqueCoders\LaravelMediaSecure\Tests\Models\User;
use CleaniqueCoders\LaravelMediaSecure\Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

function media(User $user)
{
    $testFile = __DIR__.'/stubs/test.txt';

    // Ensure the test file exists
    if (! file_exists($testFile)) {
        file_put_contents($testFile, 'Test content for media');
    }

    try {
        // Create media without specifying collection name first
        $media = $user
            ->addMedia($testFile)
            ->usingName('Test File')
            ->toMediaCollection();

        return $media;
    } catch (\Exception $e) {
        // If that fails, try with the 'default' collection
        try {
            $media = $user
                ->addMedia($testFile)
                ->usingName('Test File')
                ->toMediaCollection('default');

            return $media;
        } catch (\Exception $e2) {
            throw new \Exception('Failed to create media: '.$e2->getMessage());
        }
    }
}
