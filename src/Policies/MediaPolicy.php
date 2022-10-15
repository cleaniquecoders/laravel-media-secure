<?php

namespace Bekwoh\LaravelMediaSecure\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Gate;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \Illuminate\Foundation\Auth\User  $user
     * @param  \Spatie\MediaLibrary\MediaCollections\Models\Media  $media
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Media $media)
    {
        if (! auth()->user()) {
            return false;
        }

        if (! file_exists($media->getPath())) {
            return false;
        }

        if (! is_null(Gate::getPolicyFor($media->model))) {
            return Gate::allows('view', $media->model);
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \Illuminate\Foundation\Auth\User  $userq
     * @param  \Spatie\MediaLibrary\MediaCollections\Models\Media  $media
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function download(User $user, Media $media)
    {
        if (! auth()->user()) {
            return false;
        }

        if (! file_exists($media->getPath())) {
            return false;
        }

        if (! is_null(Gate::getPolicyFor($media->model))) {
            return Gate::allows('view', $media->model);
        }

        return true;
    }
}
