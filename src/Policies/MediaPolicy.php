<?php

namespace CleaniqueCoders\LaravelMediaSecure\Policies;

use CleaniqueCoders\LaravelMediaSecure\Enums\MediaAccess;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model's media.
     *
     * @return Response|bool
     */
    public function view(User $user, Media $media)
    {
        return $this->canAccess($user, $media, MediaAccess::VIEW);
    }

    /**
     * Determine whether the user can stream the model's media.
     *
     * @return Response|bool
     */
    public function stream(User $user, Media $media)
    {
        return $this->canAccess($user, $media, MediaAccess::STREAM);
    }

    /**
     * Determine whether the user can download the model's media.
     *
     * @return Response|bool
     */
    public function download(User $user, Media $media)
    {
        return $this->canAccess($user, $media, MediaAccess::DOWNLOAD);
    }

    /**
     * Determine whether the user can view, stream or download
     * the model's media.
     */
    private function canAccess(User $user, Media $media, MediaAccess $mediaAccess): bool
    {
        // Note: If we reach this point via policy check, $user is the authenticated user.
        // The require_auth check is handled by middleware, but we keep it here for
        // direct policy usage where user might be explicitly passed.

        if (! Storage::disk($media->disk)->exists($media->getPathRelativeToRoot())) {
            return false;
        }

        $parentPolicy = Gate::getPolicyFor($media->model);

        if (config('laravel-media-secure.strict') && is_null($parentPolicy)) {
            return false;
        }

        if (! is_null($parentPolicy)) {
            return Gate::forUser($user)->allows($mediaAccess->value, $media->model);
        }

        return true;
    }
}
