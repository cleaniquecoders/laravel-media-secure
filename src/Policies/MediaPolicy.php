<?php

namespace CleaniqueCoders\LaravelMediaSecure\Policies;

use CleaniqueCoders\LaravelMediaSecure\Enums\MediaAccess;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Gate;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model's media.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Media $media)
    {
        return $this->canAccess($user, $media, MediaAccess::VIEW);
    }

    /**
     * Determine whether the user can stream the model's media.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function stream(User $user, Media $media)
    {
        return $this->canAccess($user, $media, MediaAccess::STREAM);
    }

    /**
     * Determine whether the user can download the model's media.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function download(User $user, Media $media)
    {
        return $this->canAccess($user, $media, MediaAccess::DOWNLOAD);
    }

    /**
     * Determine whether the user can view, stream or download
     * the model's media.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    private function canAccess(User $user, Media $media, MediaAccess $mediaAccess)
    {
        if (config('laravel-media-secure.require_auth') && ! auth()->check()) {
            return false;
        }

        if (! file_exists($media->getPath())) {
            return false;
        }

        if (config('laravel-media-secure.strict') && is_null(Gate::getPolicyFor($media->model))) {
            return false;
        }

        if (! is_null(Gate::getPolicyFor($media->model))) {
            return Gate::allows($mediaAccess->value, $media->model);
        }

        return true;
    }
}
