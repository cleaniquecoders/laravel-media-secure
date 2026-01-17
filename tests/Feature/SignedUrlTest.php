<?php

use CleaniqueCoders\LaravelMediaSecure\LaravelMediaSecure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

beforeEach(function () {
    if (! Schema::hasTable('media')) {
        Artisan::call('migrate', [
            '--path' => '../../../../tests/database/migrations/',
        ]);
    }

    // Disable strict mode for testing
    config()->set('laravel-media-secure.strict', false);

    // Enable signed URLs
    config()->set('laravel-media-secure.signed.enabled', true);
    config()->set('laravel-media-secure.signed.expiration', 60);
});

describe('Signed URL Generation', function () {
    it('can generate a signed view URL', function () {
        $user = user();
        $media = media($user);

        $service = app(LaravelMediaSecure::class);
        $url = $service->signedViewUrl($media);

        expect($url)->toContain('media-signed/view/'.$media->uuid);
        expect($url)->toContain('signature=');
        expect($url)->toContain('expires=');
    });

    it('can generate a signed download URL', function () {
        $user = user();
        $media = media($user);

        $service = app(LaravelMediaSecure::class);
        $url = $service->signedDownloadUrl($media);

        expect($url)->toContain('media-signed/download/'.$media->uuid);
        expect($url)->toContain('signature=');
    });

    it('can generate a signed stream URL', function () {
        $user = user();
        $media = media($user);

        $service = app(LaravelMediaSecure::class);
        $url = $service->signedStreamUrl($media);

        expect($url)->toContain('media-signed/stream/'.$media->uuid);
        expect($url)->toContain('signature=');
    });

    it('can generate signed URL with custom expiration in minutes', function () {
        $user = user();
        $media = media($user);

        $service = app(LaravelMediaSecure::class);
        $url = $service->signedViewUrl($media, 120); // 2 hours

        expect($url)->toContain('signature=');
    });

    it('can generate signed URL with DateTime expiration', function () {
        $user = user();
        $media = media($user);

        $service = app(LaravelMediaSecure::class);
        $url = $service->signedViewUrl($media, now()->addHours(24));

        expect($url)->toContain('signature=');
    });
});

describe('Signed URL Helper Functions', function () {
    it('has signed URL helper functions', function () {
        expect(function_exists('get_signed_media_url'))->toBeTrue();
        expect(function_exists('get_signed_view_url'))->toBeTrue();
        expect(function_exists('get_signed_download_url'))->toBeTrue();
        expect(function_exists('get_signed_stream_url'))->toBeTrue();
    });

    it('generates signed URLs via helper functions', function () {
        $user = user();
        $media = media($user);

        $viewUrl = get_signed_view_url($media);
        $downloadUrl = get_signed_download_url($media);
        $streamUrl = get_signed_stream_url($media);

        expect($viewUrl)->toContain('signature=');
        expect($downloadUrl)->toContain('signature=');
        expect($streamUrl)->toContain('signature=');
    });

    it('can pass custom expiration to helper functions', function () {
        $user = user();
        $media = media($user);

        $url = get_signed_view_url($media, 30); // 30 minutes

        expect($url)->toContain('signature=');
    });
});

describe('Signed URL Access', function () {
    it('can access media with valid signed URL without authentication', function () {
        $user = user();
        $media = media($user);

        $url = get_signed_view_url($media);

        // Access without logging in - should work
        $this->get($url)
            ->assertStatus(200);
    });

    it('can download media with valid signed URL without authentication', function () {
        $user = user();
        $media = media($user);

        $url = get_signed_download_url($media);

        $this->get($url)
            ->assertStatus(200)
            ->assertHeader('Content-Disposition');
    });

    it('can stream media with valid signed URL without authentication', function () {
        $user = user();
        $media = media($user);

        $url = get_signed_stream_url($media);

        $this->get($url)
            ->assertStatus(200);
    });

    it('returns 403 for invalid signature', function () {
        $user = user();
        $media = media($user);

        // Generate URL but tamper with signature
        $url = route('media.signed', [
            'type' => 'view',
            'uuid' => $media->uuid,
            'signature' => 'invalid-signature',
            'expires' => now()->addHour()->timestamp,
        ]);

        $this->get($url)
            ->assertStatus(403);
    });

    it('returns 403 for expired signature', function () {
        $user = user();
        $media = media($user);

        // Generate URL with past expiration
        $url = URL::temporarySignedRoute(
            'media.signed',
            now()->subHour(), // Expired 1 hour ago
            [
                'type' => 'view',
                'uuid' => $media->uuid,
            ]
        );

        $this->get($url)
            ->assertStatus(403);
    });

    it('returns 404 for non-existent media with valid signature', function () {
        $url = URL::temporarySignedRoute(
            'media.signed',
            now()->addHour(),
            [
                'type' => 'view',
                'uuid' => 'non-existent-uuid',
            ]
        );

        $this->get($url)
            ->assertStatus(404);
    });

    it('returns 422 for invalid access type with valid signature', function () {
        $user = user();
        $media = media($user);

        $url = URL::temporarySignedRoute(
            'media.signed',
            now()->addHour(),
            [
                'type' => 'invalid-type',
                'uuid' => $media->uuid,
            ]
        );

        $this->get($url)
            ->assertStatus(422);
    });
});

describe('Signed URL Configuration', function () {
    it('respects default expiration from config', function () {
        config()->set('laravel-media-secure.signed.expiration', 30);

        $service = app(LaravelMediaSecure::class);

        expect($service->getDefaultExpiration())->toBe(30);
    });

    it('can check if signed URLs are enabled', function () {
        config()->set('laravel-media-secure.signed.enabled', true);
        $service = app(LaravelMediaSecure::class);
        expect($service->signedUrlsEnabled())->toBeTrue();

        config()->set('laravel-media-secure.signed.enabled', false);
        expect($service->signedUrlsEnabled())->toBeFalse();
    });
});

describe('Signed URL Caching Headers', function () {
    it('returns ETag header for signed URL access', function () {
        $user = user();
        $media = media($user);

        $url = get_signed_view_url($media);

        $this->get($url)
            ->assertStatus(200)
            ->assertHeader('ETag')
            ->assertHeader('Last-Modified')
            ->assertHeader('Cache-Control');
    });
});
