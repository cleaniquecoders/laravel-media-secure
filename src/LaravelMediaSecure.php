<?php

namespace CleaniqueCoders\LaravelMediaSecure;

use CleaniqueCoders\LaravelMediaSecure\Enums\MediaAccess;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
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
