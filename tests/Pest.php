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
    $file = __DIR__.'/file.test';
    if (file_exists($file)) {
        unlink($file);
    }

    file_put_contents($file, 'Test content');

    try {
        $media = $user
            ->addMedia($file)
            ->usingName('Test File')
            ->usingFileName('file.test')
            ->toMediaCollection('private'); // Use a private collection name

        if (! $media) {
            throw new \Exception('Media not created');
        }

        return $user->fresh()->getFirstMedia('private');
    } catch (\Exception $e) {
        var_dump($e->getMessage());
        throw $e;
    } finally {
        if (file_exists($file)) {
            unlink($file);
        }
    }
}
