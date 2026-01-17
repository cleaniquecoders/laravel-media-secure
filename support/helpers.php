<?php

use CleaniqueCoders\LaravelMediaSecure\Enums\MediaAccess;
use CleaniqueCoders\LaravelMediaSecure\LaravelMediaSecure;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/*
|--------------------------------------------------------------------------
| Standard Media URL Helpers
|--------------------------------------------------------------------------
|
| These helpers generate URLs for authenticated media access.
| Users must be logged in to access media via these URLs.
|
*/

if (! function_exists('get_media_url')) {
    /**
     * Generate an authenticated media URL for the given access type.
     */
    function get_media_url(MediaAccess $mediaAccess, Media $media): string
    {
        return route(config('laravel-media-secure.route_name'), [
            'type' => $mediaAccess->value,
            'uuid' => $media->uuid,
        ]);
    }
}

if (! function_exists('get_view_media_url')) {
    /**
     * Generate an authenticated URL for viewing media.
     */
    function get_view_media_url(Media $media): string
    {
        return get_media_url(MediaAccess::VIEW, $media);
    }
}

if (! function_exists('get_download_media_url')) {
    /**
     * Generate an authenticated URL for downloading media.
     */
    function get_download_media_url(Media $media): string
    {
        return get_media_url(MediaAccess::DOWNLOAD, $media);
    }
}

if (! function_exists('get_stream_media_url')) {
    /**
     * Generate an authenticated URL for streaming media.
     */
    function get_stream_media_url(Media $media): string
    {
        return get_media_url(MediaAccess::STREAM, $media);
    }
}

/*
|--------------------------------------------------------------------------
| Signed Media URL Helpers
|--------------------------------------------------------------------------
|
| These helpers generate time-limited, cryptographically signed URLs
| that allow unauthenticated access to media files. Useful for sharing
| media with external users or embedding in emails.
|
*/

if (! function_exists('get_signed_media_url')) {
    /**
     * Generate a signed media URL for the given access type.
     *
     * @param  \DateTimeInterface|\DateInterval|int|null  $expiration  Expiration in minutes, DateInterval, or DateTime
     */
    function get_signed_media_url(MediaAccess $mediaAccess, Media $media, $expiration = null): string
    {
        return app(LaravelMediaSecure::class)->signedUrl($mediaAccess, $media, $expiration);
    }
}

if (! function_exists('get_signed_view_url')) {
    /**
     * Generate a signed URL for viewing media.
     *
     * @param  \DateTimeInterface|\DateInterval|int|null  $expiration  Expiration in minutes, DateInterval, or DateTime
     */
    function get_signed_view_url(Media $media, $expiration = null): string
    {
        return app(LaravelMediaSecure::class)->signedViewUrl($media, $expiration);
    }
}

if (! function_exists('get_signed_download_url')) {
    /**
     * Generate a signed URL for downloading media.
     *
     * @param  \DateTimeInterface|\DateInterval|int|null  $expiration  Expiration in minutes, DateInterval, or DateTime
     */
    function get_signed_download_url(Media $media, $expiration = null): string
    {
        return app(LaravelMediaSecure::class)->signedDownloadUrl($media, $expiration);
    }
}

if (! function_exists('get_signed_stream_url')) {
    /**
     * Generate a signed URL for streaming media.
     *
     * @param  \DateTimeInterface|\DateInterval|int|null  $expiration  Expiration in minutes, DateInterval, or DateTime
     */
    function get_signed_stream_url(Media $media, $expiration = null): string
    {
        return app(LaravelMediaSecure::class)->signedStreamUrl($media, $expiration);
    }
}
