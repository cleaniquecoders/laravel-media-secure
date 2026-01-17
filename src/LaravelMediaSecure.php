<?php

namespace CleaniqueCoders\LaravelMediaSecure;

use CleaniqueCoders\LaravelMediaSecure\Enums\MediaAccess;
use DateInterval;
use DateTimeInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class LaravelMediaSecure
{
    /**
     * Generate a secure URL for viewing media.
     */
    public function viewUrl(Media $media): string
    {
        return $this->url(MediaAccess::VIEW, $media);
    }

    /**
     * Generate a secure URL for downloading media.
     */
    public function downloadUrl(Media $media): string
    {
        return $this->url(MediaAccess::DOWNLOAD, $media);
    }

    /**
     * Generate a secure URL for streaming media.
     */
    public function streamUrl(Media $media): string
    {
        return $this->url(MediaAccess::STREAM, $media);
    }

    /**
     * Generate a secure URL for the given access type and media.
     */
    public function url(MediaAccess $accessType, Media $media): string
    {
        return route(config('laravel-media-secure.route_name'), [
            'type' => $accessType->value,
            'uuid' => $media->uuid,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Signed URL Methods
    |--------------------------------------------------------------------------
    |
    | These methods generate time-limited, cryptographically signed URLs that
    | allow unauthenticated access to media files. Useful for sharing media
    | with external users or embedding in emails.
    |
    */

    /**
     * Generate a signed URL for viewing media.
     *
     * @param  DateTimeInterface|DateInterval|int|null  $expiration  Expiration time (null = use config default)
     */
    public function signedViewUrl(Media $media, DateTimeInterface|DateInterval|int|null $expiration = null): string
    {
        return $this->signedUrl(MediaAccess::VIEW, $media, $expiration);
    }

    /**
     * Generate a signed URL for downloading media.
     *
     * @param  DateTimeInterface|DateInterval|int|null  $expiration  Expiration time (null = use config default)
     */
    public function signedDownloadUrl(Media $media, DateTimeInterface|DateInterval|int|null $expiration = null): string
    {
        return $this->signedUrl(MediaAccess::DOWNLOAD, $media, $expiration);
    }

    /**
     * Generate a signed URL for streaming media.
     *
     * @param  DateTimeInterface|DateInterval|int|null  $expiration  Expiration time (null = use config default)
     */
    public function signedStreamUrl(Media $media, DateTimeInterface|DateInterval|int|null $expiration = null): string
    {
        return $this->signedUrl(MediaAccess::STREAM, $media, $expiration);
    }

    /**
     * Generate a signed URL for the given access type and media.
     *
     * @param  DateTimeInterface|DateInterval|int|null  $expiration  Expiration time in minutes, DateInterval, or DateTime (null = use config default)
     */
    public function signedUrl(MediaAccess $accessType, Media $media, DateTimeInterface|DateInterval|int|null $expiration = null): string
    {
        $expiration = $this->resolveExpiration($expiration);

        return URL::temporarySignedRoute(
            config('laravel-media-secure.signed.route_name'),
            $expiration,
            [
                'type' => $accessType->value,
                'uuid' => $media->uuid,
            ]
        );
    }

    /**
     * Check if signed URLs are enabled.
     */
    public function signedUrlsEnabled(): bool
    {
        return (bool) config('laravel-media-secure.signed.enabled', true);
    }

    /**
     * Get the default signed URL expiration time in minutes.
     */
    public function getDefaultExpiration(): int
    {
        return (int) config('laravel-media-secure.signed.expiration', 60);
    }

    /**
     * Resolve the expiration time to use.
     */
    protected function resolveExpiration(DateTimeInterface|DateInterval|int|null $expiration): DateTimeInterface|DateInterval|int
    {
        if ($expiration === null) {
            return now()->addMinutes($this->getDefaultExpiration());
        }

        if (is_int($expiration)) {
            return now()->addMinutes($expiration);
        }

        return $expiration;
    }

    /*
    |--------------------------------------------------------------------------
    | Authorization Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Check if a user can access media with the given access type.
     */
    public function canAccess(Authenticatable $user, Media $media, MediaAccess $accessType): bool
    {
        return Gate::forUser($user)->allows($accessType->value, $media);
    }

    /**
     * Check if a user can view media.
     */
    public function canView(Authenticatable $user, Media $media): bool
    {
        return $this->canAccess($user, $media, MediaAccess::VIEW);
    }

    /**
     * Check if a user can download media.
     */
    public function canDownload(Authenticatable $user, Media $media): bool
    {
        return $this->canAccess($user, $media, MediaAccess::DOWNLOAD);
    }

    /**
     * Check if a user can stream media.
     */
    public function canStream(Authenticatable $user, Media $media): bool
    {
        return $this->canAccess($user, $media, MediaAccess::STREAM);
    }

    /*
    |--------------------------------------------------------------------------
    | Configuration Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Check if authentication is required for media access.
     */
    public function requiresAuth(): bool
    {
        return (bool) config('laravel-media-secure.require_auth', true);
    }

    /**
     * Check if strict mode is enabled.
     */
    public function isStrict(): bool
    {
        return (bool) config('laravel-media-secure.strict', true);
    }
}
